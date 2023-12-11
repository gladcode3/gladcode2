/*
programa cliente. responsavel pela simulacao do ponto de vista do gladiador.
Todas funções que o usuario pode chamar diretamente estão em gladCodeAPI.
- inicia a cominicacao com o servidor
- faz chamada do setup do gladiador (lido do codigo do usuário)
- prepara o gladiador e espera os outros todos ficarem prontos
- faz a chamada do loop do gladiador (lido do código do usuário).
- executa as funções responsáveis pelo comportamento do gladiador
- termina a comunicacao com o servidor
*/

//tutorial deste site:
//http://www.binarytides.com/socket-programming-c-linux-tutorial/


#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include<stdarg.h>
#include<time.h>
#include<math.h>
#include<sys/socket.h>
#include<arpa/inet.h> //inet_addr
#include<netdb.h> //hostent

#include "gladCodeAPI.c"
#include "ptbrFunc.c"

//defines for disabled functions
#define system //
#define fopen //
 
int socket_desc;

 char *getIp(char *hostname){
    char *ip = (char*)malloc(sizeof(char)*100);
    struct hostent *he;
    struct in_addr **addr_list;
    int i;
    
    if ( (he = gethostbyname( hostname ) ) == NULL) 
    {
        //gethostbyname failed
        herror("gethostbyname");
        return NULL;
    }
     
    //Cast the h_addr_list to in_addr , since h_addr_list also has the ip address in long format only
    addr_list = (struct in_addr **) he->h_addr_list;
     
    for(i = 0; addr_list[i] != NULL; i++) 
    {
        //Return the first one;
        strcpy(ip , inet_ntoa(*addr_list[i]) );
    }
     
    printf("%s resolved to : %s\n" , hostname , ip);
    return ip;
}
 
int initClient(){
    //char *ip = getIp("localhost");
	char *ip = "127.0.0.1";
    int port = 8888;

    struct sockaddr_in server;
     
    //Create socket
    socket_desc = socket(AF_INET , SOCK_STREAM , 0);
    if (socket_desc == -1)
    {
        printf("Could not create socket");
		return 0;
    }

    server.sin_addr.s_addr = inet_addr(ip);
    server.sin_family = AF_INET;
    server.sin_port = htons( port );
 
    //Connect to remote server
	int attempts = 100;
    while (connect(socket_desc , (struct sockaddr *)&server , sizeof(server)) < 0)
    {
		/*
        puts("Error connecting to server socket. Attepting again...");
		attempts--;
		if (!attempts)
			return 0;
		*/
    }
	//printf("Connected");
    return 1;
}	

int sendMessage(char *message, char *response){
    //Send some data
    if( send(socket_desc , message , strlen(message) , 0) < 0)
    {
        puts("Send failed");
        return 0;
    }
    
    //Receive a reply from the server
    char server_reply[2000] = "";
    int read_size;
    if( read_size = recv(socket_desc, server_reply , 2000 , 0) > 0)
    {
		strcpy(response,server_reply);
		memset(server_reply,0,strlen(server_reply));
    }
    if(read_size == 0)
    {
		//puts("No reply available");
		//printf("%s\n",response);
    }
	return 1;
}

int startSim(){
	char r[10];
	sendMessage("startSimulation", r);
	return atoi(r);
}

void endSocketComm(){
	char response[10];
	sendMessage("endSocketComm", response);
}

int running(){
	char r[10];
	sendMessage("isSimRunning", r);
	return atoi(r);
}

int main(){
    initClient();
	
	setup();
	
	if (!startSim())
		return 0;
	
	while(running())
		loop();
	
	endSocketComm();
	
	return 0;
}