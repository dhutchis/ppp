#common, server, client
SRC = DieWithMessage.c acceptTCPConnection.c printSocketAddress.c setupTCPServerSocket.c
OBJ = $(SRC:.c=.o)
HEADER = ppp_client.h
#NAME = libecho_server.a #Library Name
TMS = ppp_client.c #Test Main Source
TMO = $(TMS:.c=.o) #Test Main Object
TMN = ppp_client.exe #Test Main Name
LIBS = -pthread -mt -lcurl
SUBDIRS = 
LIBPATH = -L. $(foreach DIR,$(SUBDIRS),-L./$(DIR))
RM = rm -f
# other files to include in the tar
TARINCL = #README_dhutchis_ppp_client.pdf
CC = gcc -D NDEBUG  #-Wall -Wextra -ggdb3 #-D_GNU_SOURCE

.PHONY: default all test clean cleanall fclean fcleanall re reall tarall tarhelper debug

default: server
#	@echo Options: $(NAME) all server client debug_server debug_client clean cleanall fclean fcleanall tarall

all: $(OBJ)
#	$(foreach DIR,$(SUBDIRS),$(MAKE) -C $(DIR) &&) :
#	$(AR) -rc $(NAME) $(OBJ)
#	@echo ranlib is $(RANLIB).
#	ranlib $(NAME)
server: all $(TMO)
	gcc -D NDEBUG $(LIBPATH) $(TMO) $(LIBS) $(OBJ) -o $(TMN)
debug: all $(TMO)
	gcc -ggdb3 -Wall -Wextra $(LIBPATH) $(TMO) $(LIBS) $(OBJ) -o $(TMN)
clean:
	-$(RM) *.o
	-$(RM) *~
	-$(RM) \#*
	-$(RM) *.core
cleanall: clean
	$(foreach DIR,$(SUBDIRS),$(MAKE) cleanall -C $(DIR) &&) :
fclean: clean
	-$(RM) $(NAME)
	-$(RM) $(TMN)
fcleanall: fclean
	$(foreach DIR,$(SUBDIRS),$(MAKE) fcleanall -C $(DIR) &&) :
tarhelper:
	mkdir dhutchis
	cp $(SRC) $(TMS) $(TMS_CLIENT) makefile $(HEADER) $(TARINCL) dhutchis
	$(foreach DIR,$(SUBDIRS),$(MAKE) tarhelper -C $(DIR) && mkdir dhutchis/$(DIR) && cp -R $(DIR)/dhutchis/* dhutchis/$(DIR)/ && rm -R $(DIR)/dhutchis/ &&) :
tarall: tarhelper
	tar czvf dhutchis.tar.gz dhutchis/
	rm -R dhutchis/