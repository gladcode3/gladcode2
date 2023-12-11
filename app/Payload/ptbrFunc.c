float pegaTempo(){
    return getSimTime();
}

void mudaFOR(int arg){
    setSTR(arg);
}

void mudaAGI(int arg){
    setAGI(arg);
}

void mudaINT(int arg){
    setINT(arg);
}

void mudaNome(char *arg){
    setName(arg);
}

int pegaFOR(){
    return getSTR();
}

int pegaAGI(){
    return getSTR();
}

int pegaINT(){
    return getSTR();
}

char* pegaNome(){
    char *name = (char*)malloc(sizeof(char)*100);
    strcpy(name, getName());
    return name;
}

int melhoraFOR(int n){
    return upgradeSTR(n);
}

int melhoraAGI(int n){
    return upgradeAGI(n);
}

int melhoraINT(int n){
    return upgradeINT(n);
}

float passoFrente(){
    return stepForward();
}

float passoTras(){
    return stepBack();
}

float passoEsquerda(){
    return stepLeft();
}

float passoDireita(){
    return stepRight();
}

float viraEsquerda(float ang){
    return turnLeft(ang);
}

float viraDireita(float ang){
    return turnRight(ang);
}

void vira(float ang){
    turn(ang);
}

int viraPara(float x, float y){
    return turnTo(x,y);
}

int viraParaAlvo(){
    return turnToTarget();
}

int viraParaAngulo(float ang){
    return turnToAngle(ang);
}

int viraFuiAcertado(){
    return turnToLastHit();
}

void moveFrente(float p){
    moveForward(p);
}

int movePara(float x, float y){
    return moveTo(x,y);
}

int moveParaAlvo(){
    return moveToTarget();
}

float pegaX(){
    return getX();
}

float pegaY(){
    return getY();
}

float pegaPv(){
    return getHp();
}

float pegaPh(){
    return getAp();
}

float pegaVelocidade(){
    return getSpeed();
}

float pegaDirecao(){
    return getHead();
}

float pegaDistancia(float x, float y){
    return getDist(x,y);
}

float pegaDistanciaAlvo(){
    return getDistToTarget();
}

float pegaAngulo(float x, float y){
    return getAngle(x,y);
}

int quantosInimigos(){
    return howManyEnemies();
}

int pegaInimigoProximo(){
    return getCloseEnemy();
}

int pegaInimigoDistante(){
    return getFarEnemy();
}

int pegaVidaBaixa(){
    return getLowHp();
}

int pegaVidaAlta(){
    return getHighHp();
}

float pegaXAlvo(){
    return getTargetX();
}

float pegaYAlvo(){
    return getTargetY();
}

float pegaSaudeAlvo(){
    return getTargetHealth();
}

float pegaVelocidadeAlvo(){
    return getTargetSpeed();
}

float pegaDirecaoAlvo(){
    return getTargetHead();
}

int voceMeVe(){
    return doYouSeeMe();
}

int alvoVisivel(){
    return isTargetVisible();
}

void ataqueCorpo(){
    attackMelee();
}

int ataqueDistancia(float x, float y){
    return attackRanged(x,y);
}

float tempoFuiAcertado(){
    return getLastHitTime();
}

float anguloFuiAcertado(){
    return getLastHitAngle();
}

int fuiAcertado(){
    return getHit();
}

float pegaRaioSeguro(){
    return getSafeRadius();
}

int seguroAqui(){
    return isSafeHere();
}

int seguroLa(float x, float y){
    return isSafeThere(x,y);
}

int bolaFogo(float x, float y){
    return fireball(x,y);
}

int teletransporte(float x, float y){
    return teleport(x,y);
}

int bloqueio(){
    return block();
}

int emboscada(){
    return ambush();
}

int assassinar(float x, float y){
    return assassinate(x,y);
}

int investida(){
    return charge();
}

float tempoBloqueio(){
    return getBlockTimeLeft();
}

float tempoEmboscada(){
    return getAmbushTimeLeft();
}

float tempoQueimadura(){
    return getBurnTimeLeft();
}

void mudaAparencia(char *str){
    setSpritesheet(str);
}

int estaAtordoado(){
    return isStunned();
}

int estaQueimando(){
    return isBurning();
}

int estaProtegido(){
    return isProtected();
}

int estaCorrendo(){
    return isRunning();
}

int estaLento(){
    return isSlowed();
}

void fala(char *message){
    speak(message);
}

int pegaNivel(){
    return getLvl();
}

void mudaPosicao(float x, float y){
    setPosition(x, y);
}

void mudaPv(float hp){
    setHp(hp);
}

void mudaPh(float ap){
    setAp(ap);
}

void sobeNivel(int n){
    lvlUp(n);
}

int usaItem(char *str){
    return useItem(str);
}