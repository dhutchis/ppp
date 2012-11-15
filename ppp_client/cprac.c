#include <stdio.h>
#include <string.h> // memset
#include <stdlib.h>
#include <assert.h>

#include <curl/curl.h>

int main(int argc, char** argv) {
	while(argc--) {
		printf("Arg: %s\n",*(argv));
		argv++;
	}
	
	//curl_global_init();
	
	CURL *curl;
	CURLcode res;

	curl = curl_easy_init();
	if(curl) {
		curl_easy_setopt(curl, CURLOPT_URL, "http://www.cs.stevens.edu/~dhutchis/make_conn.php");

		/* Perform the request, res will get the return code */ 
		res = curl_easy_perform(curl);
		/* Check for errors */ 
		if(res != CURLE_OK)
		  fprintf(stderr, "curl_easy_perform() failed: %s\n",
				  curl_easy_strerror(res));

		/* always cleanup */ 
		curl_easy_cleanup(curl);
	} else fprintf(stderr, "no curl from curl_easy_init()\n");
	
	return 0;
}
