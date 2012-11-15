#include "ppp_client.h"

int acceptTCPConnection(int servSock) 
{
  struct sockaddr_storage clntAddr; // Client address
  socklen_t clntAddrLen = sizeof(clntAddr); // Set length of client address structure (in-out parameter)
  int clntSock;
	
  // Wait for a client to connect
  clntSock = accept(servSock, (struct sockaddr*) &clntAddr, &clntAddrLen);
  if (clntSock < 0)
    DieWithPerrorMessage("accept() failed");

  // clntSock is connected to a client!
#ifndef NDEBUG
  fputs("Handling client ", stdout);
  printSocketAddress((struct sockaddr*) &clntAddr, stdout);
  fputc('\n', stdout);
#endif
  return clntSock;
}
