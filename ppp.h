#ifndef PPP_H_
#define PPP_H_

//#define NDEBUG
#include <assert.h>
#include <errno.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>     // memset()
#include <sys/types.h>  // size_t, pthread_t
#include <dirent.h>     // opendir(), ...
#include <sys/socket.h>
#include <netinet/in.h>	// in_addr_t, in_port_t
#include <netdb.h>      // struct addrinfo
#include <arpa/inet.h>
#include <pthread.h>
#include <unistd.h>     // close()

#define ENDL "\r\n"
//#define eprintf(...) fprintf (stderr, __VA_ARGS__)

// utilities
void DieWithMessage(const char* msg, const char* msg2);
void DieWithPerrorMessage(const char* msg);

int setupTCPClientSocket(const char *host, const char *service); // create and connect to host:service

#endif
