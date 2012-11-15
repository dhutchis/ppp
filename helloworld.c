#include <stdio.h>
#include <string.h> // memset
#include <stdlib.h>
#include <assert.h>

int main(int argc, char** argv) {
	while(argc--) {
		printf("Arg: %s\n",*(argv));
		argv++;
	}
	return 0;
}
