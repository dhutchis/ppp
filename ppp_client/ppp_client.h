#ifndef PPP_CLIENT_H_
#define PPP_CLIENT_H_

//#define NDEBUG
#include <assert.h>
#include <errno.h>
#include <stdio.h>
#include <stdlib.h>
#include <stddef.h>     // offsetof macro
#include <string.h>     // memset()
#include <sys/types.h>  // size_t, pthread_t
#include <sys/stat.h>
#include <dirent.h>     // opendir(), ...
#include <sys/socket.h>
#include <netinet/in.h>	// in_addr_t, in_port_t
#include <netdb.h>      // struct addrinfo
#include <arpa/inet.h>
#include <pthread.h>
#include <unistd.h>     // close()
#include <time.h>       // struct timespec, 
#include <sys/select.h>
#include <sys/time.h>

#include <curl/curl.h>  // use -lcurl

#define ENDL "\r\n"

// utilities
void DieWithMessage(const char* msg, const char* msg2);
void DieWithPerrorMessage(const char* msg);
void printSocketAddress(const struct sockaddr* address, FILE* stream);

int setupTCPServerSocket(const char* service); // Create, bind, and listen a new TCP server socket
int acceptTCPConnection(int servSock); // Accept a new TCP connection on a server socket

#endif
