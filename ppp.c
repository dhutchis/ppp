// Power Proxy Program!
// Dylan Hutchison

#include "ppp.h"
const unsigned int BUFSIZE = 8192; // 16384?

static void* thread_main(void* arg);	// entry point for threads
typedef struct s_thread_args {
  int sock1;
  int sock2;
} t_thread_args;

int main(int argc, char* argv[]) {
	char *a1, *a2, *p1, *p2;
	int sock1, sock2;
	
    if (argc != 5) 
		DieWithMessage("Usage: address1 port1 address2 port2","");
	a1 = argv[1]; p1 = argv[2]; a2 = argv[3]; p2 = argv[4];
	// Sanitize Inputs Here ---
	
	// make socket connections to both addresses
    sock1 = setupTCPClientSocket(a1, p1);
	if (sock1 < 0)
        DieWithMessage("setupTCPClientSocket() failed to connect to", a1);
	sock2 = setupTCPClientSocket(a2, p2);
	if (sock2 < 0)
        DieWithMessage("setupTCPClientSocket() failed to connect to", a2);
	
    // setup thread creation option
	pthread_attr_t thr_options;
    int rtnVal = pthread_attr_init(&thr_options);
    if (rtnVal != 0)
        DieWithMessage("pthread_attr_init() failed", strerror(rtnVal));
    pthread_attr_setdetachstate(&thr_options, PTHREAD_CREATE_DETACHED);
    
	t_thread_args *thrarg1, *thrarg2;
	pthread_t /*tid1=0,*/ tid2=0;
	
	thrarg1 = (t_thread_args*)malloc(sizeof(t_thread_args));
	thrarg1->sock1 = sock1; thrarg1->sock2 = sock2; // thread_main will free
	thrarg2 = (t_thread_args*)malloc(sizeof(t_thread_args));
	thrarg2->sock1 = sock2; thrarg2->sock2 = sock1;
	
	// use 2 threads - this one and another
	// have each thread receive data on one socket and forward it to the other
	rtnVal = pthread_create(&tid2, &thr_options, &thread_main, thrarg2);
	if (rtnVal != 0)
		DieWithMessage("pthread_create() failed", strerror(rtnVal));
	
    pthread_attr_destroy(&thr_options);
	(void)thread_main(thrarg1); // NEVER RETURN
    return 0;
}

// recv on sock1, forward to sock2
void* thread_main(void* arg)
{ 
	#ifndef NDEBUG
	pthread_t tid = pthread_self();
	printf("%lu is starting...\n",tid);
	#endif
    int sock1 = ((t_thread_args*)arg)->sock1;
    int sock2 = ((t_thread_args*)arg)->sock2;
    free(arg);

    while (1)
	{
		char buffer[BUFSIZE];
		ssize_t numBytesRcvd = recv(sock1, buffer, BUFSIZE, 0);
		if (numBytesRcvd < 0)
			DieWithPerrorMessage("recv() failed");
		#ifndef NDEBUG
		printf("Received: %d bytes\n",numBytesRcvd); // for logging
		#endif
		
		// Send received string and receive again until end of stream
		while (numBytesRcvd > 0) { // 0 indicates end of stream
			ssize_t numBytesSent = send(sock2, buffer, numBytesRcvd, 0);
			if (numBytesSent < 0)
				DieWithPerrorMessage("send() failed");
			else if (numBytesSent != numBytesRcvd)
				DieWithMessage("send()", "sent unexpected number of bytes");

			// See if there is more data to receive
			numBytesRcvd = recv(sock1, buffer, BUFSIZE, 0);
			if (numBytesRcvd < 0)
				DieWithPerrorMessage("recv() failed");
		}
		close(sock1);
		close(sock2); // this will interrupt the other thread
	}
    return NULL;
}
