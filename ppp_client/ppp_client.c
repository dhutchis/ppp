// Power Proxy Program Client
// Dylan Hutchison

#include "ppp_client.h"
const unsigned int BUFSIZE = 8192; // 16384?

static void* thread_main(void* arg);	// entry point for threads
typedef struct s_thread_args {
  int sock1;
  int sock2;
} t_thread_args;

int main(int argc, char* argv[]) {
	const char *name_server, *php_port, *loop_port;
	int php_server_sock, loop_server_sock, php_connection_sock, loop_connection_sock;
	
    if (argc != 4) {
        // eprintf("Usage: address1 port1 address2 port2\n");
		// exit(1);
      DieWithMessage("Usage: name_server php_port loop_port","");
    }
    
	name_server = argv[1]; php_port = argv[2]; loop_port = argv[3];
	// SANITIZE INPUTS HERE
	
	// Wait for a connection from the program on this machine that wants to communicate with the server on the other side of the PHP proxy
	// Note: currently accepts a connection from anywhere.  Should I restrict to only connections from 127.0.0.1?
    loop_server_sock = setupTCPServerSocket(php_port);
	if (loop_server_sock < 0)
        DieWithMessage("SetupTCPServerSocket() failed on loop_port", loop_server_sock);
	loop_connection_sock = acceptTCPConnection(loop_server_sock);
	close(loop_server_sock); // just 1 connection
	
	// Send HTTP Request to PHP Server
	char req_url[96]; // 56 base + 3*4+3 + 5 = 76, use 96 for safety
	snprintf(req_url, 96, "http://www.cs.stevens.edu/~dhutchis/make_conn.php?n=%s&p=%s", name_server, php_port);
	// DO HTTP REQUEST HERE
	
	
	
	
	// END HTTP REQUEST
	// Listen on php_port for the connection from the php ppp proxy.	
	php_server_sock = setupTCPServerSocket(php_port);
	if (php_server_sock < 0)
        DieWithMessage("setupTCPClientSocket() failed on php_port", php_server_sock);
	php_connection_sock = acceptTCPConnection(php_server_sock);
	close(php_server_sock); // just 1 connection
	
	// Now we just need to forward data from one to the other
	// spawn thread to handle sock2 -> sock1 communication
	// main thread will handle sock1 -> sock2 communication
	
    // setup thread creation option
	pthread_attr_t thr_options;
    int rtnVal = pthread_attr_init(&thr_options);
    if (rtnVal != 0)
        DieWithMessage("pthread_attr_init() failed", strerror(rtnVal));
    pthread_attr_setdetachstate(&thr_options, PTHREAD_CREATE_DETACHED);
	
	t_thread_args *thrarg1, *thrarg2;
	pthread_t tid1=0, tid2=0;
	
	// thr_args = (t_thread_args*)malloc(1*sizeof(t_thread_args));
	// if (!thr_args) DieWithPerrorMessage("malloc() failed");
	// thr_args->sock1 = clntSock;
	thrarg1 = (t_thread_args*)malloc(sizeof(t_thread_args));
	thrarg1->sock1 = loop_connection_sock; thrarg1->sock2 = php_connection_sock; // thread_main will free
	thrarg2 = (t_thread_args*)malloc(sizeof(t_thread_args));
	thrarg2->sock1 = php_connection_sock; thrarg2->sock2 = loop_connection_sock;
	
	// rtnVal = pthread_create(&tid1, &thr_options, &thread_main, thrarg1);
	// if (rtnVal != 0)
		// DieWithMessage("pthread_create() failed", strerror(rtnVal));
	rtnVal = pthread_create(&tid2, &thr_options, &thread_main, thrarg2); 
	if (rtnVal != 0)
		DieWithMessage("pthread_create() failed", strerror(rtnVal));    
	//printf("Created threads %lu and %lu\n",tid1,tid2);
	
    pthread_attr_destroy(&thr_options);
	(void)thread_main(thrarg1); // NEVER RETURN
    return 0;
}

// recv on sock1, forward to sock2
void* thread_main(void* arg)
{
	pthread_t tid = pthread_self(); printf("%lu is starting...\n",tid);
    int sock1 = ((t_thread_args*)arg)->sock1;
    int sock2 = ((t_thread_args*)arg)->sock2;
    free(arg);

    while (1)
	{
		char buffer[BUFSIZE];
		ssize_t numBytesRcvd = recv(sock1, buffer, BUFSIZE, 0);
		if (numBytesRcvd < 0)
			DieWithPerrorMessage("recv() failed");
		printf("Received: %d bytes\n",numBytesRcvd);
		
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
		close(sock2); // this will interrupt the other process
	}
	//printf("%lu is exiting...\n",tid);
    return NULL;
}
