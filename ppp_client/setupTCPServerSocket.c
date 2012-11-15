#include "ppp_client.h"

const int MAXPENDING = 5; // Maximum outstanding connection requests

int setupTCPServerSocket(const char* service) {
  // Construct the server address structure
  struct addrinfo addrCriteria;   // Criteria for address match
  struct addrinfo* servAddr;      // List of server addresses
  int servSock;
  int rtnVal;
	
  memset(&addrCriteria, 0, sizeof(addrCriteria)); // Zero out structure
  addrCriteria.ai_family = AF_INET;             // IPv4 ONLY
  addrCriteria.ai_flags = AI_PASSIVE;             // Accept on any address/port
  addrCriteria.ai_socktype = SOCK_STREAM;         // Only stream sockets
  addrCriteria.ai_protocol = IPPROTO_TCP;         // Only TCP protocol
	
  rtnVal = getaddrinfo(0, service, &addrCriteria, &servAddr);
  if (rtnVal != 0)
    DieWithMessage("getaddrinfo() failed", gai_strerror(rtnVal));
	
  servSock = -1;
  // Iterate through possible server addresses and choose one that works
  // (usually IPv4 is the first choice)
  struct addrinfo* addr;
  int i=0;
  for (addr = servAddr; addr != 0; addr = addr->ai_next) 
    i++;
#ifndef NDEBUG
  printf("%d possible server addresses to bind to\n", i);
#endif
  for (addr = servAddr; addr != 0; addr = addr->ai_next) {
    //if (--i)
    //continue;
    // Create a TCP socket
    servSock = socket(addr->ai_family, addr->ai_socktype, addr->ai_protocol);
    if (servSock < 0)
      continue;       // Socket creation failed; try next address
    // Try to bind to the local address and set socket to listen
    if ((bind(servSock, addr->ai_addr, addr->ai_addrlen) == 0) &&
	(listen(servSock, MAXPENDING) == 0)) {
      // Print local address of socket
      struct sockaddr_storage localAddr;
      socklen_t addrSize = sizeof(localAddr);
      if (getsockname(servSock, (struct sockaddr* ) &localAddr, &addrSize) < 0)
	DieWithPerrorMessage("getsockname() failed");
      puts("Binding to ");
      printSocketAddress((struct sockaddr* ) &localAddr, stdout);
      fputc('\n', stdout);
      break;       // Bind and listen successful
    }

    close(servSock);  // Close and try again
    servSock = -1;
  }

  // Free address list allocated by getaddrinfo()
  freeaddrinfo(servAddr);

  return servSock;
}
