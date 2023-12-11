/*
codigo inicial do servidor.
- cria o socket e prepara ele pra escutar clientes
- cria threads dos clientes
- threads dos clientes escutam o socket
- requisicoes dos clientes chama funções da gladCodeServerAPI
*/
#include<stdio.h>
#include<string.h>    //strlen
#include<stdlib.h>    //strlen
#include<stdarg.h>
#include<math.h>
#include<sys/socket.h>
#include<arpa/inet.h> //inet_addr
#include<unistd.h>    //write
#include<pthread.h> //for threading , link with lpthread
#include "explodeFunc.c"
#include "gladCodeGlobals.c"
#include "gladCodeServerCore.c"
#include "gladCodeServerAPI.c"

struct thread_param {
    int socket_desc;
    struct sockaddr_in client_address;
};

//This will handle connection for each client
void *connection_handler(void *p){
    //unload parameters
    struct thread_param param = *(struct thread_param*)p;
    //Get the socket descriptor
    int sock = param.socket_desc;
    int port = param.client_address.sin_port;
    int read_size;
    char client_message[2000] = "", reply[2000];

    //get gladid from port
    pthread_mutex_lock(&lock);
    int gladid = createGladiator(port);
    pthread_mutex_unlock(&lock);

    //Receive a message from client
    int endcomm = 0;
    
    while(!endcomm){
        read_size = recv(sock , client_message , 2000 , 0);
        if(read_size > 0){
            struct stringFunc *func = decodeFuncArg(client_message);
            
            if ((g+gladid)->hp <= 0 && (g+gladid)->time > timeInterval)
                break;
            
            //Send the message back to client
            if (strcmp(func->call,"getPort")==0){
                sprintf(reply, "%i", port);
            }
            else if (strcmp(func->call,"getId")==0){
                sprintf(reply, "%i", gladid);
            }
            else if (strcmp(func->call,"getNglad")==0){
                sprintf(reply, "%i", nglad);
            }
            else if (strcmp(func->call,"setSTR")==0){
                int v = func->arg[0].toInt;
                setSTR(gladid, v);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"getSTR")==0){
                sprintf(reply, "%i",getSTR(gladid));
            }
            else if (strcmp(func->call,"setAGI")==0){
                int v = func->arg[0].toInt;
                setAGI(gladid, v);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"getAGI")==0){
                sprintf(reply, "%i",getAGI(gladid));
            }
            else if (strcmp(func->call,"setINT")==0){
                int v = func->arg[0].toInt;
                setINT(gladid, v);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"getINT")==0){
                sprintf(reply, "%i",getINT(gladid));
            }
            else if (strcmp(func->call,"setName")==0){
                char *v = func->arg[0].toStr;
                setName(gladid, v);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"getName")==0){
                sprintf(reply, "%s",getName(gladid));
            }
            else if (strcmp(func->call,"upgradeSTR")==0){
                int n = func->arg[0].toInt;
                sprintf(reply, "%i",upgradeSTR(gladid, n));
            }
            else if (strcmp(func->call,"upgradeAGI")==0){
                int n = func->arg[0].toInt;
                sprintf(reply, "%i",upgradeAGI(gladid, n));
            }
            else if (strcmp(func->call,"upgradeINT")==0){
                int n = func->arg[0].toInt;
                sprintf(reply, "%i",upgradeINT(gladid, n));
            }
            else if (strcmp(func->call,"getX")==0){
                sprintf(reply, "%f",getX(gladid));
            }
            else if (strcmp(func->call,"getY")==0){
                sprintf(reply, "%f",getY(gladid));
            }
            else if (strcmp(func->call,"getHp")==0){
                sprintf(reply, "%f",getHp(gladid));
            }
            else if (strcmp(func->call,"getAp")==0){
                sprintf(reply, "%f",getAp(gladid));
            }
            else if (strcmp(func->call,"getSpeed")==0){
                sprintf(reply, "%f",getSpeed(gladid));
            }
            else if (strcmp(func->call,"getHead")==0){
                sprintf(reply, "%f",getHead(gladid));
            }
            else if (strcmp(func->call,"getDist")==0){
                float x = func->arg[0].toFloat;
                float y = func->arg[1].toFloat;
                sprintf(reply, "%f",getDist(gladid,x,y));
            }
            else if (strcmp(func->call,"getDistToTarget")==0){
                sprintf(reply, "%f",getDistToTarget(gladid));
            }
            else if (strcmp(func->call,"getAngle")==0){
                float x = func->arg[0].toFloat;
                float y = func->arg[1].toFloat;
                sprintf(reply, "%f",getAngle(gladid,x,y));
            }
            else if (strcmp(func->call,"howManyEnemies")==0){
                sprintf(reply, "%i",howManyEnemies(gladid));
            }
            else if (strcmp(func->call,"getCloseEnemy")==0){
                sprintf(reply, "%i",getCloseEnemy(gladid));
            }
            else if (strcmp(func->call,"getFarEnemy")==0){
                sprintf(reply, "%i",getFarEnemy(gladid));
            }
            else if (strcmp(func->call,"getLowHp")==0){
                sprintf(reply, "%i",getLowHp(gladid));
            }
            else if (strcmp(func->call,"getHighHp")==0){
                sprintf(reply, "%i",getHighHp(gladid));
            }
            else if (strcmp(func->call,"getTargetSpeed")==0){
                sprintf(reply, "%f",getTargetSpeed(gladid));
            }
            else if (strcmp(func->call,"getTargetHead")==0){
                sprintf(reply, "%f",getTargetHead(gladid));
            }
            else if (strcmp(func->call,"getTargetHealth")==0){
                sprintf(reply, "%f",getTargetHealth(gladid));
            }
            else if (strcmp(func->call,"getTargetX")==0){
                sprintf(reply, "%f",getTargetX(gladid));
            }
            else if (strcmp(func->call,"getTargetY")==0){
                sprintf(reply, "%f",getTargetY(gladid));
            }
            else if (strcmp(func->call,"doYouSeeMe")==0){
                sprintf(reply, "%i", doYouSeeMe(gladid));
            }
            else if (strcmp(func->call,"isTargetVisible")==0){
                sprintf(reply, "%i", isTargetVisible(gladid));
            }
            else if (strcmp(func->call,"getSimTime")==0){
                sprintf(reply, "%.1f", getSimTime(gladid));
            }
            else if (strcmp(func->call,"getSimCounters")==0){
                sprintf(reply, "%s", getSimCounters());
            }
            else if (strcmp(func->call,"startSimulation")==0){
                sprintf(reply, "%i", startSimulation(gladid));
            }
            else if (strcmp(func->call,"isSimRunning")==0){
                sprintf(reply, "%i", isSimRunning(gladid));
            }
            else if (strcmp(func->call,"getLastHitTime")==0){
                sprintf(reply, "%f", getLastHitTime(gladid));
            }
            else if (strcmp(func->call,"getLastHitAngle")==0){
                sprintf(reply, "%f", getLastHitAngle(gladid));
            }
            else if (strcmp(func->call,"turnToLastHit")==0){
                sprintf(reply, "%i", turnToLastHit(gladid));
            }
            else if (strcmp(func->call,"getHit")==0){
                sprintf(reply, "%i",getHit(gladid));
            }
            else if (strcmp(func->call,"getSafeRadius")==0){
                sprintf(reply, "%f", getSafeRadius(gladid));
            }
            else if (strcmp(func->call,"isSafeHere")==0){
                sprintf(reply, "%i", isSafeHere(gladid));
            }
            else if (strcmp(func->call,"isSafeThere")==0){
                float x = func->arg[0].toFloat;
                float y = func->arg[1].toFloat;
                sprintf(reply, "%i", isSafeThere(gladid, x, y));
            }
            else if (strcmp(func->call,"stepForward")==0){
                float d = stepForward(gladid);
                sprintf(reply, "%f", d);
            }
            else if (strcmp(func->call,"stepBack")==0){
                float d = stepBack(gladid);
                sprintf(reply, "%f", d);
            }
            else if (strcmp(func->call,"stepLeft")==0){
                float d = stepLeft(gladid);
                sprintf(reply, "%f", d);
            }
            else if (strcmp(func->call,"stepRight")==0){
                float d = stepRight(gladid);
                sprintf(reply, "%f", d);
            }
            else if (strcmp(func->call,"turnLeft")==0){
                float ang = func->arg[0].toFloat;
                float r = turnLeft(gladid, ang);
                sprintf(reply, "%f", r);
            }
            else if (strcmp(func->call,"turnRight")==0){
                float ang = func->arg[0].toFloat;
                float r = turnRight(gladid, ang);
                sprintf(reply, "%f", r);
            }
            else if (strcmp(func->call,"turn")==0){
                float ang = func->arg[0].toFloat;
                turn(gladid, ang);
            }
            else if (strcmp(func->call,"turnTo")==0){
                float x = func->arg[0].toFloat;
                float y = func->arg[1].toFloat;
                sprintf(reply, "%i", turnTo(gladid, x, y));
            }
            else if (strcmp(func->call,"turnToTarget")==0){
                sprintf(reply, "%i", turnToTarget(gladid));
            }
            else if (strcmp(func->call,"turnToAngle")==0){
                float v = func->arg[0].toFloat;
                sprintf(reply, "%i", turnToAngle(gladid, v));
            }
            else if (strcmp(func->call,"moveForward")==0){
                float p = func->arg[0].toFloat;
                moveForward(gladid, p);
            }
            else if (strcmp(func->call,"moveTo")==0){
                float x = func->arg[0].toFloat;
                float y = func->arg[1].toFloat;
                sprintf(reply, "%i", moveTo(gladid, x, y));
            }
            else if (strcmp(func->call,"moveToTarget")==0){
                sprintf(reply, "%i", moveToTarget(gladid));
            }
            else if (strcmp(func->call,"attackMelee")==0){
                attackMelee(gladid);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"attackRanged")==0){
                float x = func->arg[0].toFloat;
                float y = func->arg[1].toFloat;
                sprintf(reply, "%i", attackRanged(gladid, x, y));
            }
            else if (strcmp(func->call,"fireball")==0){
                float x = func->arg[0].toFloat;
                float y = func->arg[1].toFloat;
                sprintf(reply, "%i", fireball(gladid, x, y));
            }
            else if (strcmp(func->call,"teleport")==0){
                float x = func->arg[0].toFloat;
                float y = func->arg[1].toFloat;
                sprintf(reply, "%i", teleport(gladid, x, y));
            }
            else if (strcmp(func->call,"block")==0){
                sprintf(reply, "%i", block(gladid));
            }
            else if (strcmp(func->call,"ambush")==0){
                sprintf(reply, "%i", ambush(gladid));
            }
            else if (strcmp(func->call,"assassinate")==0){
                float x = func->arg[0].toFloat;
                float y = func->arg[1].toFloat;
                sprintf(reply, "%i", assassinate(gladid, x, y));
            }
            else if (strcmp(func->call,"charge")==0){
                sprintf(reply, "%i", charge(gladid));
            }
            else if (strcmp(func->call,"getBlockTimeLeft")==0){
                float t = getBlockTimeLeft(gladid);
                sprintf(reply, "%f", t);
            }
            else if (strcmp(func->call,"getAmbushTimeLeft")==0){
                float t = getAmbushTimeLeft(gladid);
                sprintf(reply, "%f", t);
            }
            else if (strcmp(func->call,"getBurnTimeLeft")==0){
                float t = getBurnTimeLeft(gladid);
                sprintf(reply, "%f", t);
            }
            else if (strcmp(func->call,"isStunned")==0){
                int r = isStunned(gladid);
                sprintf(reply, "%i", r);
            }
            else if (strcmp(func->call,"isBurning")==0){
                int r = isBurning(gladid);
                sprintf(reply, "%i", r);
            }
            else if (strcmp(func->call,"isProtected")==0){
                int r = isProtected(gladid);
                sprintf(reply, "%i", r);
            }
            else if (strcmp(func->call,"isRunning")==0){
                int r = isRunning(gladid);
                sprintf(reply, "%i", r);
            }
            else if (strcmp(func->call,"isSlowed")==0){
                int r = isSlowed(gladid);
                sprintf(reply, "%i", r);
            }
            else if (strcmp(func->call,"speak")==0){
                char *m = func->arg[0].toLongStr;
                speak(gladid, m);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"getLvl")==0){
                int r = getLvl(gladid);
                sprintf(reply, "%i", r);
            }
            else if (strcmp(func->call,"breakpoint")==0){
                char *m = func->arg[0].toLongStr;
                breakpoint(gladid, m);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"setPosition")==0){
                float x = func->arg[0].toFloat;
                float y = func->arg[1].toFloat;
                setPositionSB(gladid, x, y);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"setHp")==0){
                float hp = func->arg[0].toFloat;
                setHpSB(gladid, hp);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"setAp")==0){
                float ap = func->arg[0].toFloat;
                setApSB(gladid, ap);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"lvlUp")==0){
                int n = func->arg[0].toInt;
                lvlUpSB(gladid, n);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"useItem")==0){
                char *str = func->arg[0].toStr;
                sprintf(reply, "%i", useItem(gladid, str));
            }
            else if (strcmp(func->call,"setSlots")==0){
                char *str = func->arg[0].toStr;
                setSlots(gladid, str);
                sprintf(reply, "done");
            }
            else if (strcmp(func->call,"isItemReady")==0){
                char *str = func->arg[0].toStr;
                sprintf(reply, "%i", isItemReady(gladid, str));
            }
            else if (strcmp(func->call,"endSocketComm")==0){
                endcomm = 1;
                sprintf(reply, "done");
            }
            else{
                sprintf(reply, "%s", "UNKNOWN COMMAND");
            }
            memset(client_message, 0, sizeof(client_message));
            write(sock , reply , strlen(reply));
        }
        else{
            if(read_size == 0)
                printf("Client disconnected");
            else if(read_size == -1)
                printf("Error");
            endcomm = 1;
        }
    }
    
    return 0;
}
 
int main(int argc , char *argv[]){
    struct timeval tv;
    gettimeofday(&tv,NULL);
    long unsigned int seci = tv.tv_sec;
    long unsigned int useci = tv.tv_usec;
    
    // set names with index in from globals
    setItemNames();

    int i;
    nglad = atoi(argv[1]);
    g = NULL;
    p = NULL;
    
    outArq = fopen("usercode/simlog","w");
    if (!outArq)
        printf("Error creating file");
            
    int socket_desc, c;
    struct sockaddr_in server , client;
    char message[2000] = "";
    
    if (pthread_mutex_init(&lock, NULL) != 0){
        printf("\n mutex init failed\n");
        return 1;
    }
    if (pthread_cond_init(&cond, NULL) != 0){
        printf("\n condition init failed\n");
        return 1;
    }
    
    //Create socket
    socket_desc = socket(AF_INET , SOCK_STREAM , 0);
    if (socket_desc == -1)
    {
        printf("Could not create socket");
    }
     
    //Prepare the sockaddr_in structure
    server.sin_family = AF_INET;
    server.sin_addr.s_addr = INADDR_ANY;
    server.sin_port = htons( 8888 );
     
    //Bind
    if( bind(socket_desc,(struct sockaddr *)&server , sizeof(server)) < 0)
    {
        puts("bind failed");
        return 1;
    }
     
    //Listen
    listen(socket_desc , 3);
     
    //Accept and incoming connection
    c = sizeof(struct sockaddr_in);
    pthread_t socket_thread[nglad];
    int new_socket;
    struct thread_param p[nglad];

    for( i=0 ; i<nglad ; i++){
        char name[10];
        sprintf(name,"usercode/code%i.py",i);

        // is a C file
        if (fopen(name,"r") == NULL) {
            sprintf(name,"usercode/code%i",i);
            FILE *f = NULL;

            struct timeval wait_start, wait_now;
            gettimeofday(&wait_start,NULL);
            long unsigned int sec_diff;
            do{
                f = fopen(name,"r");
                gettimeofday(&wait_now,NULL);
                sec_diff = wait_now.tv_sec - wait_start.tv_sec;
            }while (f == NULL && sec_diff < 10);
            if (f == NULL){
                endsim = 1;
                printf("CLIENT TIMEOUT");
                break;
            }
            else
                fclose(f);
        }
        // python file
        else{
            FILE *f = NULL;
            do {
                f = fopen("usercode/errorc.txt", "r");
            } while(f == NULL);
            char text[20] = "";
            fgets(text, 10, f);
            if (strlen(text) > 0){
                endsim = 1;
                printf("PYTHON ERROR");
                break;
            }
        }

        new_socket = accept(socket_desc, (struct sockaddr *)&client, (socklen_t*)&c);
                
        p[i].socket_desc = new_socket;
        p[i].client_address = client;
        
        
        if( pthread_create( &socket_thread[i] , NULL ,  connection_handler , &p[i])){
            printf("Could not create thread.\n");
        }
    }
    if (new_socket < 0){
        perror("accept failed");
        return 1;
    }
    
    /*
    for( i=0 ; i<nglad; i++){
        //printf("esperando %i\n",i);
        pthread_join( socket_thread[i] , NULL);
    }
    */
    
    while(!endsim)
        usleep(10000);

    //cJSON *jsonArray = cJSON_Parse(outString);
    //char *str = cJSON_Print(jsonArray);
    
    pthread_mutex_destroy(&lock);
    pthread_cond_destroy(&cond);
     
    gettimeofday(&tv,NULL);
    long unsigned int secf = tv.tv_sec;
    long unsigned int usecf = tv.tv_usec;
    
    //printf("\nprocess ended after %lu.%06lu seconds\n",secf-seci,usecf-useci);
    return 0;
}
