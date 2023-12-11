/*
contém as funções que podem ser chamadas diretamente pelo usuário.
Estas funções somente enviam sinais do cliente para o servidor. O servidor é quem executa as funções e devolve a resposta.
Desta maneira somente com a sintaxe dos sinais, pode-se fazer o port facilmente para qualquer linguagem.
*/

float getSimTime(){
    char r[10];
    sendMessage("getSimTime", r);
    return atof(r);
}

void setSTR(int arg){
    char message[50], response[10];
    sprintf(message, "setSTR %i", arg);
    sendMessage(message,response);
}

int getSTR(){
    char response[10];
    sendMessage("getSTR", response);
    return atoi(response);
}

void setAGI(int arg){
    char message[50], response[10];
    sprintf(message, "setAGI %i", arg);
    sendMessage(message,response);
}

int getAGI(){
    char response[10];
    sendMessage("getAGI", response);
    return atoi(response);
}

void setINT(int arg){
    char message[50], response[10];
    sprintf(message, "setINT %i", arg);
    sendMessage(message,response);
}

int getINT(){
    char response[10];
    sendMessage("getINT", response);
    return atoi(response);
}


void setName(char *name){
    char m[100], *r, bla[100];
    int i;
    //faz isso pra trocar todos espeços por #
    strcpy(bla,name);
    for (i=0 ; name[i] != '\0' ; i++){
        if (name[i] == ' ')
            bla[i] = '#';
    }
    sprintf(m, "setName %s", bla);
    sendMessage(m, r);
}

char* getName(){
    char *response = (char*)malloc(sizeof(char)*100);
    sendMessage("getName", response);
    return response;
}

int upgradeSTR(int n){
    char message[50], response[10];
    sprintf(message, "upgradeSTR %i", n);
    sendMessage(message,response);
    return atoi(response);
}

int upgradeAGI(int n){
    char message[50], response[10];
    sprintf(message, "upgradeAGI %i", n);
    sendMessage(message,response);
    return atoi(response);
}

int upgradeINT(int n){
    char message[50], response[10];
    sprintf(message, "upgradeINT %i", n);
    sendMessage(message,response);
    return atoi(response);
}

float stepForward(){
    char r[10];
    sendMessage("stepForward", r);
    return atof(r);
}

float stepBack(){
    char r[10];
    sendMessage("stepBack", r);
    return atof(r);
}

float stepLeft(){
    char r[10];
    sendMessage("stepLeft", r);
    return atof(r);
}

float stepRight(){
    char r[10];
    sendMessage("stepRight", r);
    return atof(r);
}

float turnLeft(float ang){
    char m[100], r[10];
    sprintf(m, "turnLeft %f", ang);
    sendMessage(m, r);
    return atof(r);
}

float turnRight(float ang){
    char m[100], r[10];
    sprintf(m, "turnRight %f", ang);
    sendMessage(m, r);
    return atof(r);
}

void turn(float ang){
    char m[100], r[10];
    sprintf(m, "turn %f", ang);
    sendMessage(m, r);
}

int turnTo(float x, float y){
    char m[100], r[10];
    sprintf(m, "turnTo %f %f", x, y);
    sendMessage(m, r);
    return atoi(r);
}

int turnToTarget(){
    char m[100], r[10];
    sprintf(m, "turnToTarget");
    sendMessage(m, r);
    return atoi(r);
}

int turnToAngle(float ang){
    char m[100], r[10];
    sprintf(m, "turnToAngle %f", ang);
    sendMessage(m, r);
    return atoi(r);
}

void moveForward(float p){
    char m[100], r[10];
    sprintf(m, "moveForward %f", p);
    sendMessage(m, r);
}

int moveTo(float x, float y){
    char m[100], r[10];
    sprintf(m, "moveTo %f %f", x, y);
    sendMessage(m, r);
    return atoi(r);
}

int moveToTarget(){
    char r[10];
    sendMessage("moveToTarget", r);
    return atoi(r);
}

float getX(){
    char r[10];
    sendMessage("getX", r);
    return atof(r);
}

float getY(){
    char r[10];
    sendMessage("getY", r);
    return atof(r);
}

float getHp(){
    char r[10];
    sendMessage("getHp", r);
    return atof(r);
}

float getAp(){
    char r[10];
    sendMessage("getAp", r);
    return atof(r);
}

float getSpeed(){
    char r[10];
    sendMessage("getSpeed", r);
    return atof(r);
}

float getHead(){
    char r[10];
    sendMessage("getHead", r);
    return atof(r);
}

float getDist(float x, float y){
    char m[100], r[10];
    sprintf(m, "getDist %f %f", x, y);
    sendMessage(m, r);
    return atof(r);
}

float getDistToTarget(){
    char r[10];
    sendMessage("getDistToTarget", r);
    return atof(r);
}

float getAngle(float x, float y){
    char m[100], r[10];
    sprintf(m, "getAngle %f %f", x, y);
    sendMessage(m, r);
    return atof(r);
}

int howManyEnemies(){
    char r[10];
    sendMessage("howManyEnemies", r);
    return atoi(r);
}

int getCloseEnemy(){
    char r[10];
    sendMessage("getCloseEnemy", r);
    return atoi(r);
}

int getFarEnemy(){
    char r[10];
    sendMessage("getFarEnemy", r);
    return atoi(r);
}

int getLowHp(){
    char r[10];
    sendMessage("getLowHp", r);
    return atoi(r);
}

int getHighHp(){
    char r[10];
    sendMessage("getHighHp", r);
    return atoi(r);
}

float getTargetX(){
    char r[10];
    sendMessage("getTargetX", r);
    return atof(r);
}

float getTargetY(){
    char r[10];
    sendMessage("getTargetY", r);
    return atof(r);
}

float getTargetHealth(){
    char r[10];
    sendMessage("getTargetHealth", r);
    return atof(r);
}

float getTargetSpeed(){
    char r[10];
    sendMessage("getTargetSpeed", r);
    return atof(r);
}

float getTargetHead(){
    char r[10];
    sendMessage("getTargetHead", r);
    return atof(r);
}

int doYouSeeMe(){
    char r[10];
    sendMessage("doYouSeeMe", r);
    return atoi(r);
}

int isTargetVisible(){
    char r[10];
    sendMessage("isTargetVisible", r);
    return atoi(r);
}

void attackMelee(){
    char r[10];
    sendMessage("attackMelee", r);
}

int attackRanged(float x, float y){
    char m[100], r[10];
    sprintf(m, "attackRanged %f %f", x, y);
    sendMessage(m, r);
    return atoi(r);
}

float getLastHitTime(){
    char response[10];
    sendMessage("getLastHitTime", response);
    return atof(response);
}

float getLastHitAngle(){
    char response[10];
    sendMessage("getLastHitAngle", response);
    return atof(response);
}

int turnToLastHit(){
    char r[10];
    sendMessage("turnToLastHit", r);
    return atoi(r);
}

int getHit(){
    char r[10];
    sendMessage("getHit", r);
    return atoi(r);
}

float getSafeRadius(){
    char response[10];
    sendMessage("getSafeRadius", response);
    return atof(response);
}

int isSafeHere(){
    char r[10];
    sendMessage("isSafeHere", r);
    return atoi(r);
}

int isSafeThere(float x, float y){
    char m[100], r[10];
    sprintf(m, "isSafeThere %f %f", x, y);
    sendMessage(m, r);
    return atoi(r);
}

int fireball(float x, float y){
    char m[100], r[10];
    sprintf(m, "fireball %f %f", x, y);
    sendMessage(m, r);
    return atoi(r);
}

int teleport(float x, float y){
    char m[100], r[10];
    sprintf(m, "teleport %f %f", x, y);
    sendMessage(m, r);
    return atoi(r);
}

int block(){
    char r[10];
    sendMessage("block", r);
    return atoi(r);
}

int ambush(){
    char r[10];
    sendMessage("ambush", r);
    return atoi(r);
}

int assassinate(float x, float y){
    char m[100], r[10];
    sprintf(m, "assassinate %f %f", x, y);
    sendMessage(m, r);
    return atoi(r);
}

int charge(){
    char r[10];
    sendMessage("charge", r);
    return atoi(r);
}

float getBlockTimeLeft(){
    char r[10];
    sendMessage("getBlockTimeLeft", r);
    return atof(r);
}

float getAmbushTimeLeft(){
    char r[10];
    sendMessage("getAmbushTimeLeft", r);
    return atof(r);
}

float getBurnTimeLeft(){
    char r[10];
    sendMessage("getBurnTimeLeft", r);
    return atof(r);
}

void setSpritesheet(char *str){
    //faz nada mesmo
}

int isStunned(){
    char r[10];
    sendMessage("isStunned", r);
    return atoi(r);
}

int isBurning(){
    char r[10];
    sendMessage("isBurning", r);
    return atoi(r);
}

int isProtected(){
    char r[10];
    sendMessage("isProtected", r);
    return atoi(r);
}

int isRunning(){
    char r[10];
    sendMessage("isRunning", r);
    return atoi(r);
}

int isSlowed(){
    char r[10];
    sendMessage("isSlowed", r);
    return atoi(r);
}
void speak(char *format, ...){
    va_list arg;
    va_start(arg, format);

    char message[1000] = "";
    while (*format != '\0' && strlen(message) < 1000) {
        if (*format == '%') {
            format++;
            if (*format == '%')
                sprintf(message, "%s%%", message);
            else if (*format == 'c')
                sprintf(message, "%s%c", message, va_arg(arg, int));
            else if (*format == 's')
                sprintf(message, "%s%s", message, va_arg(arg, char*));
            else if (*format == 'i' || *format == 'd')
                sprintf(message, "%s%i", message, va_arg(arg, int));
            else if (*format == 'f')
                sprintf(message, "%s%f", message, va_arg(arg, double));
            else if (*format == '.'){
                char f[10] = "%s%.0f";
                f[4] = *(format + 1);
                format += 2;
                sprintf(message, f, message, va_arg(arg, double));
            }
        }
        else {
            sprintf(message, "%s%c", message, *format);
        }
        format++;
    }
    va_end(arg);
    message[999] = '\0';

    char m[1000], apiMessage[1000], r[10];
    strncpy(m, message, 1000);
    sprintf(apiMessage, "speak %s", m);
    apiMessage[999] = '\0';
    sendMessage(apiMessage, r);
}

int getLvl(){
    char r[10];
    sendMessage("getLvl", r);
    return atoi(r);
}

void breakpoint(char *message){
    char m[256], r[10];
    sprintf(m, "breakpoint %s", message);
    sendMessage(m, r);
}

void setPosition(float x, float y){
    char message[50], response[10];
    sprintf(message, "setPosition %f %f", x, y);
    sendMessage(message,response);
}

void setHp(float hp){
    char message[50], response[10];
    sprintf(message, "setHp %f", hp);
    sendMessage(message,response);
}

void setAp(float ap){
    char message[50], response[10];
    sprintf(message, "setAp %f", ap);
    sendMessage(message,response);
}

void lvlUp(int n){
    char message[50], response[10];
    sprintf(message, "lvlUp %i", n);
    sendMessage(message,response);
}

int useItem(char *str){
    char m[100], r[10];
    sprintf(m, "useItem %s", str);
    sendMessage(m, r);
    return atoi(r);
}

void setSlots(char *str){
    char message[50], response[10];
    sprintf(message, "setSlots %s", str);
    sendMessage(message,response);
}

int isItemReady(char *str){
    char m[100], r[10];
    sprintf(m, "isItemReady %s", str);
    sendMessage(m, r);
    return atoi(r);
}