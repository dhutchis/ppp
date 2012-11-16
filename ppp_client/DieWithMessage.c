#include "ppp_client.h"
void DieWithMessage(const char* msg, const char* msg2)
{ 
  if (msg) {
    fputs(msg, stderr); 
    if (msg2) {
      fputs(" : ", stderr);
      fputs(msg2, stderr);
    }
    fputc('\n',stderr);  
  }
  exit(1);  
}  
void DieWithPerrorMessage(const char* msg)
{
  perror(msg);
  fputc('\n',stderr); 
  exit(1);
}
