/*
contem funcoes que foram chamadas pelo servidor para atender requisições do cliente
*/

int getIndex(int port){
    int i;
    for (i=0 ; i<nglad ; i++){
        if ((g+i)->port == port)
            return i;
    }
}

void setSTR(int gladid, int n){
    if ( (g+gladid)->lvl == 0){
        (g+gladid)->STR = n;
        (g+gladid)->hp = 100 + n*10;
        (g+gladid)->maxhp = (g+gladid)->hp;
        (g+gladid)->mdmg = 0.75*n+5;
    }
}

void setAGI(int gladid, int n){
    if ( (g+gladid)->lvl == 0){
        (g+gladid)->AGI = n;
        (g+gladid)->spd = 1 + n*0.05;
        (g+gladid)->as = 0.5 + n*0.05;
        (g+gladid)->ts = 90 + n*9;
        (g+gladid)->rdmg = n*0.4+5;
    }
}

void setINT(int gladid, int n){
    if ( (g+gladid)->lvl == 0){
        (g+gladid)->INT = n;
        (g+gladid)->ap = 100 + n*10;
        (g+gladid)->maxap = (g+gladid)->ap;
        (g+gladid)->cs = 0.5 + n*0.05;
        (g+gladid)->sdmg = n*0.5;
    }
}

int getSTR(int gladid){
    return (g+gladid)->STR;
}

int getAGI(int gladid){
    return (g+gladid)->AGI;
}

int getINT(int gladid){
    return (g+gladid)->INT;
}

void setName(int gladid, char *v){
    if ( (g+gladid)->lvl == 0){
        int i;
        char user[50] = "";
        for (i=0 ; i<strlen(v) ; i++){
            if (v[i] == '@'){
                strcpy(user, v+i+1);
                v[i] = '\0';
            }
        }
        strcpy((g+gladid)->name, v);
        if (strcmp(user,"") != 0)
            strcpy((g+gladid)->user, user);
    }
}

char* getName(int gladid){
    return (g+gladid)->name;
}

void setSlots(int gladid, char *str){
    if ( (g+gladid)->lvl == 0){
        int i;
        char *e = str, *s = str;
        for (i=0 ; i<N_SLOTS ; i++){
            while (*e != ',' && *e != '\0'){
                e++;
            }
            char n[100];
            strncpy(n, s, e-s);
            n[e-s] = '\0';
            (g+gladid)->items[i] = atoi(n);
            e++;
            s = e;
        }
    }
}

int upgradeSTR(int gladid, int n){
    if (n > (g+gladid)->up)
        n = (g+gladid)->up;
    if (n > 0){
        (g+gladid)->STR += n;
        (g+gladid)->hp += 10 * n;
        (g+gladid)->maxhp += 10 * n;
        (g+gladid)->mdmg += 0.75 * n;
        (g+gladid)->up -= n;
        return 1;
    }
    return 0;
}

int upgradeAGI(int gladid, int n){
    if (n > (g+gladid)->up)
        n = (g+gladid)->up;
    if (n > 0){
        (g+gladid)->AGI += n;
        (g+gladid)->spd += 0.05 * n;
        (g+gladid)->as += 0.05 * n;
        (g+gladid)->ts += 9 * n;
        (g+gladid)->rdmg += 0.4 * n;
        (g+gladid)->up -= n;
        return 1;
    }
    return 0;
}

int upgradeINT(int gladid, int n){
    if (n > (g+gladid)->up)
        n = (g+gladid)->up;
    if (n > 0){
        (g+gladid)->INT += n;
        (g+gladid)->ap += 10 * n;
        (g+gladid)->maxap += 10 * n;
        (g+gladid)->cs += 0.05 * n;
        (g+gladid)->sdmg += 0.5 * n;
        (g+gladid)->up -= n;
        return 1;
    }
    return 0;
}

float getSimTime(int gladid){
    return (g+gladid)->time;
}

char* getSimCounters(){
    char *b = (char*)malloc(sizeof(char) * 100);
    strcpy(b,"");
    int i;
    for (i=0 ; i<nglad ; i++)
        sprintf(b, "%s%.1f ", b, (g+i)->time);
    return b;
}

int isSimRunning(int gladid){
    if ( (g+gladid)->action == ACTION_NONE ){
        updateSimulation(gladid);
    }
    (g+gladid)->action = ACTION_NONE;
    
    checkAlive();
    if (endsim){
        fclose(outArq);
        return 0;
    }
    
    return 1;
}

int startSimulation(int gladid){
    int i;
    int timeout = 10000;
    if (!checkSetup(gladid))
        return 0;
        
    (g+gladid)->time = 0;

    //wait until every glad is ready to begin
    do{
        usleep(1);
        for (i=0 ; i<nglad ; i++){
            if ( (g+i)->time != 0 )
                break;
        }
        timeout--;
    }while(i < nglad && timeout > 0);
    if (timeout <= 0)
        return 0;
    readytostart++;
    while(readytostart < nglad);
    
    return 1;
}

//move para frente
float stepForward(int gladid){
    appendCode(gladid, "stepForward()");
    if ((g+gladid)->hp > 0){
        
        float d = moveForwardUnsafe(gladid);
        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;
        
        
        waitForLockedStatus(gladid);
        
        return d;
    }
    return 0;
}

//move pra tras
float stepBack(int gladid){
    appendCode(gladid, "stepBack()");
    if ((g+gladid)->hp > 0 && !endsim){
        
        float hip = -(g+gladid)->spd*timeInterval;
        if ((g+gladid)->buffs[BUFF_MOVEMENT].timeleft > 0)
            hip *= (g+gladid)->buffs[BUFF_MOVEMENT].value;

        float ang = (g+gladid)->head;
        float dx, dy;
        calcSidesFromAngleDist(&dx, &dy, hip, ang);
        float oldx = (g+gladid)->x;
        float oldy = (g+gladid)->y;
        (g+gladid)->x += dx;
        (g+gladid)->y -= dy;
        preventCollision(gladid, dx, -dy);
        preventLeaving(gladid);
        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;
        
        
        waitForLockedStatus(gladid);
        
        return getDistUnsafe(gladid, oldx, oldy); 
    }
    return 0;
}

//strafe para a esquerda
float stepLeft(int gladid){
    appendCode(gladid, "stepLeft()");
    if ((g+gladid)->hp > 0 && !endsim){
        
        float hip = (g+gladid)->spd*timeInterval;
        if ((g+gladid)->buffs[BUFF_MOVEMENT].timeleft > 0)
            hip *= (g+gladid)->buffs[BUFF_MOVEMENT].value;

        float ang = (g+gladid)->head-90;
        float dx, dy;
        calcSidesFromAngleDist(&dx, &dy, hip, ang);
        float oldx = (g+gladid)->x;
        float oldy = (g+gladid)->y;
        (g+gladid)->x += dx;
        (g+gladid)->y -= dy;
        preventCollision(gladid, dx, -dy);
        preventLeaving(gladid);
        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;

        
        waitForLockedStatus(gladid);
        
        return getDistUnsafe(gladid, oldx, oldy); 
    }
    return 0;
}

//strafe para a direita
float stepRight(int gladid){
    appendCode(gladid, "stepRight()");
    if ((g+gladid)->hp > 0 && !endsim){
        
        float hip = (g+gladid)->spd*timeInterval;
        if ((g+gladid)->buffs[BUFF_MOVEMENT].timeleft > 0)
            hip *= (g+gladid)->buffs[BUFF_MOVEMENT].value;

        float ang = (g+gladid)->head+90;
        float dx, dy;
        calcSidesFromAngleDist(&dx, &dy, hip, ang);
        float oldx = (g+gladid)->x;
        float oldy = (g+gladid)->y;
        (g+gladid)->x += dx;
        (g+gladid)->y -= dy;
        preventCollision(gladid, dx, -dy);
        preventLeaving(gladid);
        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;

        
        waitForLockedStatus(gladid);
        
        return getDistUnsafe(gladid, oldx, oldy); 
    }
    return 0;
}


//anda para a frente um número de passos
void moveForward(int gladid, float p){
    appendCode(gladid, "moveForward(%.1f)", p);
    if (endsim || (g+gladid)->hp <= 0)
        return;
    if ((g+gladid)->hp > 0){
        float t = 0;
        while(t < p){
            t += moveForwardUnsafe(gladid);
            if (t > p){
                float dx, dy;
                float a = (g+gladid)->head;
                calcSidesFromAngleDist(&dx, &dy, t-p, a);
                (g+gladid)->x -= dx;
                (g+gladid)->y -= dy;
            }
            
            (g+gladid)->action = ACTION_MOVEMENT;
            (g+gladid)->lockedfor = timeInterval;
            waitForLockedStatus(gladid);
        }
    }
}

//vira para o ponto, e anda em direcao a ele
int moveTo(int gladid, float x, float y){
    appendCode(gladid, "moveTo(%.1f, %.1f)", x, y);
    if (endsim || (g+gladid)->hp <= 0)
        return 1;
    if ((g+gladid)->hp > 0){
        
        int end = moveToUnsafe(gladid, x, y);

        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;

        
        waitForLockedStatus(gladid);
        
        return end;
    }
    return 0;
}

//vira para o alvo fixado, e anda em direcao a ele
int moveToTarget(int gladid){
    appendCode(gladid, "moveToTarget()");
    if (endsim || (g+gladid)->hp <= 0)
        return 1;
    if ((g+gladid)->hp > 0 && isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        
        int end = moveToUnsafe(gladid, (g+target)->x, (g+target)->y);

        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;
        
        waitForLockedStatus(gladid);
        
        return end;
    }
    return 0;
}

//vira a visao para direita
float turnRight(int gladid, float ang){
    appendCode(gladid, "turnRight(%.1f)", ang);
    if ((g+gladid)->hp > 0 && !endsim){
                
        float r = turnStepUnsafe(gladid, getNormalAngle(ang));
        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;
        
        
        waitForLockedStatus(gladid);
        return r;
    }
    return 0;
}

//vira a visao para esquerda
float turnLeft(int gladid, float ang){
    appendCode(gladid, "turnLeft(%.1f)", ang);
    if ((g+gladid)->hp > 0 && !endsim){
        float r = turnStepUnsafe(gladid, getNormalAngle(-ang));
        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;
        
        waitForLockedStatus(gladid);
        return r;
    }
    return 0;
}

//se vira na direcao de um ponto
int turnTo(int gladid, float x, float y){
    appendCode(gladid, "turnTo(%.1f, %.1f)", x, y);
    if (endsim)
        return 1;
    if ((g+gladid)->hp > 0){
        
        int end = turnToUnsafe(gladid, x, y);
        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;

        waitForLockedStatus(gladid);
        
        return end;
    }
    return 0;
}

//se vira na direcao do alvo fixado
int turnToTarget(int gladid){
    appendCode(gladid, "turnToTarget()");
    if (endsim)
        return 1;

    if ((g+gladid)->hp <= 0)
        return 0;
    else if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        int end = turnToUnsafe(gladid, (g+target)->x, (g+target)->y);
        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;

        waitForLockedStatus(gladid);
        
        return end;
    }
    else
        return 0;
}

//virar x graus. numeros negativo viram no antihorario. angulos maiores que 360 significam mais de uma volta
void turn(int gladid, float ang){
    appendCode(gladid, "turn(%.1f)", ang);
    if (endsim)
        return;
    if ((g+gladid)->hp > 0){
        
        float t = 0;
        int sig = 1;
        if (ang < 0){
            sig = -1;
            ang = -ang;
        }
        
        while (t < ang){
            (g+gladid)->head += (g+gladid)->ts * timeInterval * sig;
            (g+gladid)->head = getNormalAngle((g+gladid)->head);
            t += (g+gladid)->ts * timeInterval;
            
            if (t > ang){
                (g+gladid)->head -= (t - ang) * sig;
                (g+gladid)->head = getNormalAngle((g+gladid)->head);
            }
            
            (g+gladid)->action = ACTION_MOVEMENT;
            (g+gladid)->lockedfor = timeInterval;
            
            waitForLockedStatus(gladid);
        }
    }
}

//se vira para uma direcao escolhida
int turnToAngle(int gladid, float ang){
    appendCode(gladid, "turnToAngle(%.1f)", ang);
    if (endsim)
        return 1;
    if ((g+gladid)->hp > 0){
        float dx,dy;
        calcSidesFromAngleDist(&dx, &dy, 10, ang);

        int end = turnToUnsafe(gladid, (g+gladid)->x + dx, (g+gladid)->y - dy);
        (g+gladid)->action = ACTION_MOVEMENT;
        (g+gladid)->lockedfor = timeInterval;

        waitForLockedStatus(gladid);
        
        return end;
    }
    return 0;
}

float getX(int gladid){
    return (g+gladid)->x;
}

float getY(int gladid){
    return (g+gladid)->y;
}

float getHp(int gladid){
    return (g+gladid)->hp;
}

float getAp(int gladid){
    return (g+gladid)->ap;
}

float getSpeed(int gladid){
    float spd = (g+gladid)->spd;
    if ((g+gladid)->buffs[BUFF_MOVEMENT].timeleft > 0)
        spd *= (g+gladid)->buffs[BUFF_MOVEMENT].value;

    return spd;
}

float getHead(int gladid){
    return (g+gladid)->head;
}

float getDist(int gladid, float x, float y){
    float r;
    if (x == -1 || y == -1)
        r = 9999;
    else
        r = getDistUnsafe(gladid, x, y);
    return r;
}

float getDistToTarget(int gladid){
    float r = -1;
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        float x = (g+target)->x;
        float y = (g+target)->y;
        r = getDistUnsafe(gladid, x, y);
    }
    return r;
}

//recebe um ponto, retorna o angulo o ponto do glad e o ponto recebido
float getAngle(int gladid, float x, float y){
    return getAngleUnsafe(gladid, x, y);
}

//retorna quantos inimigos o glad enxerga no campo de visao
int howManyEnemies(int gladid){
    int i, cont=0;
    for (i=0 ; i<nglad ; i++){
        if (i != gladid && (g+i)->hp > 0 && (g+i)->buffs[BUFF_INVISIBLE].timeleft <= 0){
            float dist = getDistUnsafe(gladid, (g+i)->x, (g+i)->y);
            float ang = getNormalAngle(getAngle(gladid, (g+i)->x, (g+i)->y) - (g+gladid)->head);
            if ( dist <= (g+gladid)->vis && (ang <= (g+gladid)->vrad/2 || ang >= 360-(g+gladid)->vrad/2) )
                cont++;
        }
    }
    
    return cont;
}

//tranca no inimigo mais próximo dentro do campo de visão
int getCloseEnemy(int gladid){
    float ang2, dist2;
    int i, closeri=-1, lowerdist;
    for (i=0 ; i<nglad ; i++){
        if (i != gladid && (g+i)->hp > 0 && (g+i)->buffs[BUFF_INVISIBLE].timeleft <= 0){
            float dist = getDistUnsafe(gladid, (g+i)->x, (g+i)->y);
            float ang = getNormalAngle(getAngle(gladid, (g+i)->x, (g+i)->y) - (g+gladid)->head);
            if ( dist <= (g+gladid)->vis && (ang <= (g+gladid)->vrad/2 || ang >= 360-(g+gladid)->vrad/2) ){
                if (closeri == -1 || dist < lowerdist){
                    ang2 = ang;
                    dist2 = dist;
                    lowerdist = dist;
                    closeri = i;
                }
            }
        }
    }
    if(closeri == -1)
        return 0;
    else{
        (g+gladid)->targetlocked = (g+closeri)->port;
        //printf("api: dist:%f ang:%f glad:%i\n",dist2,ang2,closeri);
        return 1;
    }
}

//tranca no inimigo mais longe dentro do campo de visão
int getFarEnemy(int gladid){
    int i, fari=-1, higherdist;
    for (i=0 ; i<nglad ; i++){
        if (i != gladid && (g+i)->hp > 0 && (g+i)->buffs[BUFF_INVISIBLE].timeleft <= 0){
            float dist = getDistUnsafe(gladid, (g+i)->x, (g+i)->y);
            float ang = getNormalAngle(getAngle(gladid, (g+i)->x, (g+i)->y) - (g+gladid)->head);
            if ( dist <= (g+gladid)->vis && (ang <= (g+gladid)->vrad/2 || ang >= 360-(g+gladid)->vrad/2) ){
                if (fari == -1 || dist > higherdist){
                    higherdist = dist;
                    fari = i;
                }
            }
        }
    }
    if(fari == -1)
        return 0;
    else{
         (g+gladid)->targetlocked = (g+fari)->port;
        return 1;
    }
}

//tranca no inimigo de menor hp dentro do campo de visão
int getLowHp(int gladid){
    int i, loweri=-1;
    for (i=0 ; i<nglad ; i++){
        if (i != gladid && (g+i)->hp > 0 && (g+i)->buffs[BUFF_INVISIBLE].timeleft <= 0){
            float dist = getDistUnsafe(gladid, (g+i)->x, (g+i)->y);
            float ang = getNormalAngle(getAngle(gladid, (g+i)->x, (g+i)->y) - (g+gladid)->head);
            if ( dist <= (g+gladid)->vis && (ang <= (g+gladid)->vrad/2 || ang >= 360-(g+gladid)->vrad/2) ){
                if (loweri == -1 || (g+i)->hp < (g+loweri)->hp)
                    loweri = i;
            }
        }
    }
    if(loweri == -1)
        return 0;
    else{
        (g+gladid)->targetlocked = (g+loweri)->port;
         return 1;
    }
}

//tranca no inimigo de maior hp dentro do campo de visão
int getHighHp(int gladid){
    int i, higheri=-1;
    for (i=0 ; i<nglad ; i++){
        if (i != gladid && (g+i)->hp > 0 && (g+i)->buffs[BUFF_INVISIBLE].timeleft <= 0){
            float dist = getDistUnsafe(gladid, (g+i)->x, (g+i)->y);
            float ang = getNormalAngle(getAngle(gladid, (g+i)->x, (g+i)->y) - (g+gladid)->head);
            if ( dist <= (g+gladid)->vis && (ang <= (g+gladid)->vrad/2 || ang >= 360-(g+gladid)->vrad/2) ){
                if (higheri == -1 || (g+i)->hp > (g+higheri)->hp)
                    higheri = i;
            }
        }
    }
    if(higheri == -1)
        return 0;
    else{
        (g+gladid)->targetlocked = (g+higheri)->port;
         return 1;
    }
}

float getTargetHead(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        return (g+target)->head;
    }
    else
        return -1;
}

float getTargetSpeed(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        float spd = (g+target)->spd;
        if ((g+target)->buffs[BUFF_MOVEMENT].timeleft > 0)
            spd *= (g+target)->buffs[BUFF_MOVEMENT].value;

        return spd;
    }
    else
        return -1;
}

//retorna a porcentagem de vida do alvo trancado
float getTargetHealth(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        return (float)((g+target)->hp) / (g+target)->maxhp;
    }
    else
        return -1;
}

float getTargetX(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        return (g+target)->x;
    }
    else
        return -1;
}

float getTargetY(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        return (g+target)->y;
    }
    else
        return -1;
}

//se o gladiador está no campo de visao do alvo, retorna 1
int doYouSeeMe(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);	
        float dist = getDistUnsafe(gladid, (g+target)->x, (g+target)->y);
        float ang = getNormalAngle(getAngle(gladid, (g+target)->x, (g+target)->y) - (g+target)->head + 180);
        if ( dist <= (g+target)->vis && (ang <= (g+target)->vrad/2 || ang >= 360-(g+target)->vrad/2) ){
            return 1;
        }
    }
    return 0;
}

int isTargetVisible(int gladid){
    return isLockedTargetVisibleUnsafe(gladid);
}

//ataca inimigo em frente num raio de 180g
void attackMelee(int gladid){
    appendCode(gladid, "attackMelee()");
    if ((g+gladid)->hp > 0){
        (g+gladid)->action = ACTION_MELEE_ATTACK;
        (g+gladid)->lockedfor = 1/(g+gladid)->as/2;
        waitForLockedStatus(gladid);
        
        attackMeleeUnsafe(gladid, 1);
        
        (g+gladid)->lockedfor = 1/(g+gladid)->as/2;
        waitForLockedStatus(gladid);
    }
}

//lana um projetil em direcao ao ponto
int attackRanged(int gladid, float x, float y){
    appendCode(gladid, "attackRanged(%.1f, %.1f)", x, y);
    int r = 0;
    if ((g+gladid)->hp > 0){
        if (turnToUnsafe(gladid, x,y) && checkBounds(x,y)){
            int projectiletype = PROJECTILE_TYPE_ATTACK;
            if ((g+gladid)->buffs[BUFF_INVISIBLE].timeleft > 0)
                projectiletype = PROJECTILE_TYPE_STUN;

            (g+gladid)->action = ACTION_RANGED_ATTACK;
            (g+gladid)->lockedfor = 1/(g+gladid)->as/2;
            waitForLockedStatus(gladid);
            
            float spdx, spdy;
            calcSidesFromAngleDist(&spdx, &spdy, 1.5, (g+gladid)->head);
            
            launchProjectile(gladid, (g+gladid)->x, (g+gladid)->y, (g+gladid)->rdmg, spdx, -spdy, projectiletype);

            (g+gladid)->lockedfor = 1/(g+gladid)->as/2;
            waitForLockedStatus(gladid);
            
            r = 1;
        }
        else{
            (g+gladid)->action = ACTION_MOVEMENT;
            (g+gladid)->lockedfor = timeInterval;
            waitForLockedStatus(gladid);
        }
    }
    return r;
}

float getLastHitTime(int gladid){
    return (g+gladid)->time - (g+gladid)->lasthittime;
}

float getLastHitAngle(int gladid){
    return (g+gladid)->lasthitangle;
}

//se vira na direcao do alvo fixado
int turnToLastHit(int gladid){
    appendCode(gladid, "turnToLastHit()");
    float angle = getLastHitAngle(gladid);
    return turnToAngle(gladid, angle);
}

int getHit(int gladid){
    int notif = (g+gladid)->lasthitnotification;
    (g+gladid)->lasthitnotification = 0;
    return notif;
}


float getSafeRadius(int gladid){
    float startdist = getDistAB(0,0,screenW/2,screenH/2);
    float spread = ((g+gladid)->time - POISON_TIME) / POISON_SPEED;
    float safe = startdist - spread;

    if ((g+gladid)->time < POISON_TIME)
        safe = startdist;

    if (safe < 0)
        return 0;
    else
        return safe;
}

//verifica se onde o gladiador está tem poison
int isSafeHere(int gladid){
    if ((g+gladid)->hp <= 0 || endsim)
        return 1;
    float mydist = getDistUnsafe(gladid,screenW/2,screenH/2);
    if (mydist < getSafeRadius(gladid))
        return 1;
    else
        return 0;
}

//verifica se tem poison no ponto
int isSafeThere(int gladid, float x, float y){
    if ((g+gladid)->hp <= 0 || endsim)
        return 1;
    
    float dx = screenW/2 - x;
    float dy = screenH/2 - y;
    float dist = sqrt( pow(dx,2) + pow(dy,2) );
    
    if (dist < getSafeRadius(gladid))
        return 1;
    else
        return 0;
}


//quanto tempo para acabar o buff de protecao
float getBlockTimeLeft(int gladid){
    return (g+gladid)->buffs[BUFF_RESIST].timeleft;
}

//quanto tempo para acabar o buff de invisibilidade
float getAmbushTimeLeft(int gladid){
    return (g+gladid)->buffs[BUFF_INVISIBLE].timeleft;
}

//quanto tempo para acabar a queimadura
float getBurnTimeLeft(int gladid){
    return (g+gladid)->buffs[BUFF_BURN].timeleft;
}

//verifica se o alvo está atordoado
int isStunned(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        if ( (g+target)->buffs[BUFF_STUN].timeleft > 0 )
            return 1;
        else
            return 0;
    }
    else{
        return -1;
    }
}

//verifica se o alvo está queimando
int isBurning(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        if ( (g+target)->buffs[BUFF_BURN].timeleft > 0 )
            return 1;
        else
            return 0;
    }
    else{
        return -1;
    }
}

//verifica se o alvo está com resistencia
int isProtected(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        if ( (g+target)->buffs[BUFF_RESIST].timeleft > 0 )
            return 1;
        else
            return 0;
    }
    else{
        return -1;
    }
}

//verifica se o alvo está correndo
int isRunning(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        if ( (g+target)->buffs[BUFF_MOVEMENT].timeleft > 0 && (g+target)->buffs[BUFF_MOVEMENT].value > 1)
            return 1;
        else
            return 0;
    }
    else{
        return -1;
    }
}

//verifica se o alvo está lento
int isSlowed(int gladid){
    if (isLockedTargetVisibleUnsafe(gladid)){
        int target = getLockedTarget(gladid);
        if ( (g+target)->buffs[BUFF_MOVEMENT].timeleft > 0 && (g+target)->buffs[BUFF_MOVEMENT].value < 1)
            return 1;
        else
            return 0;
    }
    else{
        return -1;
    }
}

//lanca um projetil do tipo fireball
int fireball(int gladid, float x, float y){
    appendCode(gladid, "fireball(%.1f, %.1f)", x, y);
    int r = 0;
    if ((g+gladid)->hp > 0){
        if ((g+gladid)->ap >= abilitycost[ABILITY_FIREBALL] && checkBounds(x,y)){
            if (turnToUnsafe(gladid, x,y)){
                (g+gladid)->action = ABILITY_FIREBALL;
                (g+gladid)->lockedfor = 1/(g+gladid)->cs/2;
                waitForLockedStatus(gladid);
                
                (g+gladid)->ap -= abilitycost[ABILITY_FIREBALL];
                float spdx, spdy;
                calcSidesFromAngleDist(&spdx, &spdy, 1, (g+gladid)->head);
                launchProjectile(gladid, (g+gladid)->x, (g+gladid)->y, (g+gladid)->sdmg * 0.6, spdx, -spdy, PROJECTILE_TYPE_FIREBALL);
                r = 1;
                
                (g+gladid)->lockedfor = 1/(g+gladid)->cs/2;
                waitForLockedStatus(gladid);
            }
            else{
                (g+gladid)->action = ACTION_MOVEMENT;
                (g+gladid)->lockedfor = timeInterval;
                waitForLockedStatus(gladid);
            }
        }
        else{
            (g+gladid)->lockedfor = timeInterval;
            waitForLockedStatus(gladid);
        }
    }
    return r;
}

//vai para o ponto, ou o mais proximo que conseguir dele
int teleport(int gladid, float x, float y){
    appendCode(gladid, "teleport(%.1f, %.1f)", x, y);
    int r = 0;
    if ((g+gladid)->hp > 0){
        if ((g+gladid)->ap >= abilitycost[ABILITY_TELEPORT] && checkBounds(x,y)){
            (g+gladid)->action = ABILITY_TELEPORT;
            (g+gladid)->lockedfor = 1/(g+gladid)->cs/2;
            waitForLockedStatus(gladid);

            float newhead = getAngleUnsafe(gladid, x, y);
            (g+gladid)->head = newhead;
                
            if (getDistUnsafe(gladid, x, y) <= (g+gladid)->sdmg + 5){
                float dx = x - (g+gladid)->x;
                float dy = y - (g+gladid)->y;
                (g+gladid)->x = x;
                (g+gladid)->y = y;
                preventCollision(gladid, dx, dy);
                r = 1;
            }
            else{
                float rx, ry;
                calcSidesFromMaxDist(gladid, x, y, (g+gladid)->sdmg + 5, &rx, &ry);
                (g+gladid)->x += rx;
                (g+gladid)->y += ry;
                preventCollision(gladid, x, y);
            }
            preventLeaving(gladid);
            (g+gladid)->ap -= abilitycost[ABILITY_TELEPORT];

            (g+gladid)->lockedfor = 1/(g+gladid)->cs/2;
            waitForLockedStatus(gladid);
        }
        else{
            (g+gladid)->lockedfor = timeInterval;
            waitForLockedStatus(gladid);
        }
    }
    else
        return 1;
    return r;
}

//se move com velocidade aumentada para o alvo fixado, e entao ataca.
int charge(int gladid){
    appendCode(gladid, "charge()");
    int r = 0;
    if ((g+gladid)->hp > 0){
        
        if (isLockedTargetVisibleUnsafe(gladid) && (g+gladid)->ap >= abilitycost[ABILITY_CHARGE]){
            int target = getLockedTarget(gladid);

            (g+gladid)->ap -= abilitycost[ABILITY_CHARGE];
            addBuff(gladid, BUFF_MOVEMENT, 1/(g+gladid)->cs, 4);

            //se move em direcao ao alvo
            float destx = (g+target)->x;
            float desty = (g+target)->y;

            //bonus damage = 1...5
            float bonusdmg = getDistUnsafe(gladid, destx, desty) / VIS_RANGE * 2.5;
            while(getDistUnsafe(gladid, destx, desty) > 1){
                if ((g+gladid)->buffs[BUFF_MOVEMENT].timeleft <= timeInterval){
                    addBuff(gladid, BUFF_MOVEMENT, timeInterval*2, 4);
                }
                moveToUnsafe(gladid, destx, desty);
                (g+gladid)->action = ABILITY_CHARGE;
                (g+gladid)->lockedfor = timeInterval;

                if ( (g+target)->buffs[BUFF_INVISIBLE].timeleft <= 0 && getDistUnsafe(gladid, (g+target)->x, (g+target)->y) <= (g+gladid)->vis ){
                    destx = (g+target)->x;
                    desty = (g+target)->y;
                }
                
                waitForLockedStatus(gladid);

                if ((g+gladid)->hp <= 0)
                    return 0;
            }

            turnToUnsafe(gladid, (g+target)->x, (g+target)->y);
            attackMeleeUnsafe(gladid, bonusdmg);
            (g+gladid)->action = ABILITY_CHARGE;
            if ((g+target)->buffs[BUFF_MOVEMENT].timeleft <= 0 || (g+target)->buffs[BUFF_MOVEMENT].value < 1)
                addBuff(target , BUFF_MOVEMENT, 5, exp(-0.067 * (g+gladid)->STR));
            r = 1;

            if ( (g+gladid)->buffs[BUFF_MOVEMENT].timeleft > 0){
                (g+gladid)->lockedfor = (g+gladid)->buffs[BUFF_MOVEMENT].timeleft;
                (g+gladid)->buffs[BUFF_MOVEMENT].timeleft = timeInterval;
            }
            else
                (g+gladid)->lockedfor = 0;
        }
        else{
            (g+gladid)->lockedfor = timeInterval;
        }

        waitForLockedStatus(gladid);
    }
    return r;
}

//ganha buff de protecao para ataques pela frente
int block(int gladid){
    appendCode(gladid, "block()");
    int r = 0;
    if ((g+gladid)->hp > 0){
        if ((g+gladid)->ap >= abilitycost[ABILITY_BLOCK]){
            (g+gladid)->action = ABILITY_BLOCK;
            (g+gladid)->lockedfor = 1/(g+gladid)->cs/2;
            waitForLockedStatus(gladid);

            addBuff(gladid, BUFF_RESIST, 7, 0.1 + (float)(g+gladid)->STR / ((g+gladid)->STR + 16));

            (g+gladid)->ap -= abilitycost[ABILITY_BLOCK];
            r = 1;

            (g+gladid)->lockedfor = 1/(g+gladid)->cs/2;
            waitForLockedStatus(gladid);
        }
        else{
            (g+gladid)->lockedfor = timeInterval;
            waitForLockedStatus(gladid);
        }
    }
    else
        return 1;
    return r;
}

//ataca alvo no ponto, se o alvo nao enxergar o gladiador causa dano extra, e outro adicional caso esteja atordoado
int assassinate(int gladid, float x, float y){
    appendCode(gladid, "assassinate(%.1f, %.1f)", x, y);
    int r = 0;
    if ((g+gladid)->hp > 0){
        if ((g+gladid)->ap >= abilitycost[ABILITY_ASSASSINATE] && isLockedTargetVisibleUnsafe(gladid)){
            int seen = doYouSeeMe(gladid);
            (g+gladid)->ap -= abilitycost[ABILITY_ASSASSINATE];
            while (!turnToUnsafe(gladid, x, y));
        
            int target = getLockedTarget(gladid);

            int projectiletype = PROJECTILE_TYPE_ATTACK;
            if ((g+gladid)->buffs[BUFF_INVISIBLE].timeleft > 0)
                projectiletype = PROJECTILE_TYPE_STUN;

            float damage;
            int bonus = 0;
            if (!seen || (g+gladid)->buffs[BUFF_INVISIBLE].timeleft > 0)
                bonus++;

            if ((g+target)->buffs[BUFF_STUN].timeleft > 0)
                bonus++;

            if (bonus == 2)
                damage = (g+gladid)->rdmg * 4;
            else if (bonus == 1)
                damage = (g+gladid)->rdmg * 2;
            else
                damage = (g+gladid)->rdmg;
            /*
            if (bonus == 2)
                (g+gladid)->ap += abilitycost[ABILITY_ASSASSINATE];
            */
            
            (g+gladid)->action = ABILITY_ASSASSINATE;
            (g+gladid)->lockedfor = 1/(g+gladid)->cs/2;
            waitForLockedStatus(gladid);
            
            float spdx, spdy;
            calcSidesFromAngleDist(&spdx, &spdy, 2, (g+gladid)->head);

            launchProjectile(gladid, (g+gladid)->x, (g+gladid)->y, damage, spdx, -spdy, projectiletype);

            r = 1;
            (g+gladid)->lockedfor = 1/(g+gladid)->cs/2;
            waitForLockedStatus(gladid);
        }
        else{
            (g+gladid)->lockedfor = timeInterval;
            waitForLockedStatus(gladid);
        }
    }
    return r;
}

//ganha buff de invisibilidade, impossiblitando que inimigos enxergue o gladiador e faz que proximo ataque cause stun
int ambush(int gladid){
    appendCode(gladid, "ambush()");
    int r = 0;
    if ((g+gladid)->hp > 0){
        if ((g+gladid)->ap >= abilitycost[ABILITY_AMBUSH]){
            (g+gladid)->action = ABILITY_AMBUSH;
            (g+gladid)->lockedfor = 1/(g+gladid)->cs/2;
            waitForLockedStatus(gladid);

            addBuff(gladid, BUFF_INVISIBLE, 1 + (g+gladid)->AGI * 0.1, 0);

            (g+gladid)->ap -= abilitycost[ABILITY_AMBUSH];
            r = 1;

            (g+gladid)->lockedfor = 1/(g+gladid)->cs/2;
            waitForLockedStatus(gladid);
        }
        else{
            (g+gladid)->lockedfor = timeInterval;
            waitForLockedStatus(gladid);
        }
    }
    else
        return 1;
    return r;
}

//mostra uma mensagem de fala
void speak(int gladid, char *message){
    strncpy((g+gladid)->message, message, 250);

    char *newline = strstr((g+gladid)->message, "\n");
    if (newline != NULL)
        *newline = '\0';

    (g+gladid)->message[249] = '\0';
    (g+gladid)->msgtime = 3;
    (g+gladid)->msgtype = MSG_SPEAK;
}

//retorna o nível do gladiador
int getLvl(int gladid){
    return (g+gladid)->lvl;
}

//envia uma fala do tipo breakpoint, para o render interpretar como uma pausa
void breakpoint(int gladid, char *message){
    strncpy((g+gladid)->message, message, 250);

    (g+gladid)->message[249] = '\0';
    (g+gladid)->msgtime = timeInterval;
    (g+gladid)->msgtype = MSG_BREAKPOINT;
}

// altera a posição do gladiador (sandbox)
void setPositionSB(int gladid, float x, float y){
    (g+gladid)->x = x;
    (g+gladid)->y = y;
}

// muda o hp do gladiador (sandbox)
void setHpSB(int gladid, float hp){
    (g+gladid)->hp = hp;
    if ((g+gladid)->hp > (g+gladid)->maxhp)
        (g+gladid)->hp = (g+gladid)->maxhp;
}

// muda o ap do gladiador (sandbox)
void setApSB(int gladid, float ap){
    (g+gladid)->ap = ap;
    if ((g+gladid)->ap > (g+gladid)->maxap)
        (g+gladid)->ap = (g+gladid)->maxap;
}

// faz o gladiador subir de nivel (sandbox)
void lvlUpSB(int gladid, int n){
    (g+gladid)->up += POINTS_LVL_UP * n;
    (g+gladid)->lvl += n;
}

// faz o gladiador usar um item
int useItem(int gladid, char *item){
    if (endsim || (g+gladid)->hp <= 0){
        return 0;
    }
    else{
        (g+gladid)->action = ACTION_NONE;

        int i, r = 0;
        for (i=0 ; i<N_SLOTS ; i++){
            int id = (g+gladid)->items[i];
            if (id == -1 || (id != 0 && strcmp(item, itemList[id]) == 0)){
                (g+gladid)->items[i] = 0;
                (g+gladid)->action = ACTION_ITEM;
                (g+gladid)->lockedfor = timeInterval;
                r = itemEffect(gladid, item);
                break;
            }
        }

        if (r){
            appendCode(gladid, "useItem(\\\"%s\\\")", item);
            waitForLockedStatus(gladid);
        }

        return r;
    }
}

// verifica se o item está disponível para uso
int isItemReady(int gladid, char *item){
    if (endsim || (g+gladid)->hp <= 0){
        return 0;
    }
    else{
        int i;
        for (i=0 ; i<N_SLOTS ; i++){
            int id = (g+gladid)->items[i];
            if (id == -1 || (id != 0 && strcmp(item, itemList[id]) == 0)){
                return 1;
            }
        }
        return 0;
    }
}
