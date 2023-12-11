/*
funções necessarias para o gerenciamento da simulação, que não foram requisições diretas do cliente.
*/

//converte de graus para radianos
float gToRad(float g){
    return g*M_PI/180;
}

//converte de rad para graus
float radToG(float r){
    return 180*r/M_PI;
}

//ajusta angulos para estarem entrr 0-360, onde 0==360==norte
float getNormalAngle(float ang){
    while (ang >= 360)
        ang -= 360;
    while (ang < 0)
        ang += 360;
    return ang;
}

float getAngleFromAB(float xa, float ya, float xb, float yb){
    float dx = xb - xa;
    float dy = ya - yb;
    float ang;
    if (dy == 0 && dx == 0)
        return 0;
    else if (dy > 0)
        ang = radToG(atan(dx/dy));
    else if (dy < 0)
        ang = radToG(atan(dx/dy))+180;
    else
        ang = 90 * (dx/fabs(dx));
    return getNormalAngle(ang);
}

//recebe hipotenusa e angulo, calcula calcula e atribui por referencia os catetos
void calcSidesFromAngleDist(float *dx, float *dy, float d, float a){
    *dx = sin(gToRad(a))*d;
    *dy = cos(gToRad(a))*d;
}

//verifica se coordenada está dentro da arena
int checkBounds(float x, float y){
    if (x >= 0 && x <= 25 && y >= 0 && y <= 25)
        return 1;
    else
        return 0;
}

//impede que o glad saia da tela
void preventLeaving(int gladid){
    if ( (g+gladid)->y > screenH)
        (g+gladid)->y = screenH;
    if ( (g+gladid)->y < 0)
        (g+gladid)->y = 0;
    if ( (g+gladid)->x > screenW)
        (g+gladid)->x = screenW;
    if ( (g+gladid)->x < 0)
        (g+gladid)->x = 0;
}

float getDistUnsafe(int gladid, float x, float y){
    float dx = (g+gladid)->x - x;
    float dy = (g+gladid)->y - y;
    return sqrt( pow(dx,2) + pow(dy,2) );
}

//calcula por semelhança de triangulos os catetos maxx, e maxy, a partir do triangulo de catetos destx, desty e hipotenusa maxdist
void calcSidesFromMaxDist(int gladid, float destx, float desty, float maxdist, float *maxx, float *maxy){
    float dx1, dy1, dh1;
    dh1 = getDistUnsafe(gladid, destx, desty);
    dx1 = destx - (g+gladid)->x;
    dy1 = desty - (g+gladid)->y;
    if (dh1 > 0){
        *maxx = maxdist/dh1 * dx1;
        *maxy = maxdist/dh1 * dy1;
    }
    else{
        *maxx = dx1;
        *maxy = dy1;
    }
}

float getAngleUnsafe(int gladid, float x, float y) {
    return getAngleFromAB((g+gladid)->x, (g+gladid)->y, x, y);
}

float getDistAB(float x1, float y1, float x2, float y2){
    float dx = x2 - x1;
    float dy = y2 - y1;
    return sqrt( pow(dx,2) + pow(dy,2) );
}

//impede que um gladiador invada o hitbox de outro
void preventCollision(int gladid, float lastdx, float lastdy){
    int i;
    float h;
    for (i=0 ; i<nglad ; i++){
        if (i != gladid && (g+i)->hp > 0){
            h = getDistUnsafe(gladid, (g+i)->x, (g+i)->y );
            if (h < GLAD_HITBOX){
                if (h == 0){
                    if (lastdx == 0 && lastdy == 0){
                        (g+gladid)->x += (rand()%3)*GLAD_HITBOX-GLAD_HITBOX;
                        (g+gladid)->y += (rand()%3)*GLAD_HITBOX-GLAD_HITBOX;
                    }
                    else{
                        (g+gladid)->x -= lastdx;
                        (g+gladid)->y -= lastdy;
                    }
                    h = getDistUnsafe(gladid, (g+i)->x, (g+i)->y );
                }
                
                float dx, dy, dfx=0, dfy=0;
                dx = (g+i)->x - (g+gladid)->x;
                dy = (g+i)->y - (g+gladid)->y;
                                
                dfx = GLAD_HITBOX * dx / h;
                dfy = GLAD_HITBOX * dy / h;
                
                (g+gladid)->x = (g+i)->x - dfx;
                (g+gladid)->y = (g+i)->y - dfy;
            }
        }
    }
}

int getXpToNextLvl(int gladid){
    return XP_FIRSTLVL * pow( (1 + XP_FACTOR), (g+gladid)->lvl - 1);
}

void setXp(int gladid, float dmg, int enemy){
    //200 de vida inicial + 20 por nivel (considera que upa 2 STR por nível)
    //xp é o percentual de dano causado na vida esperada do inimigo,

    int lifeatlvl = (200 + ((g+gladid)->lvl - 1) * 20 );

    float xp = dmg / lifeatlvl * 100;
    
    (g+gladid)->xp += round(xp);
    int tonext = getXpToNextLvl(gladid);
    if ((g+gladid)->xp >= tonext && (g+gladid)->hp > 0){
        float recovered = 35 + 5 * (g+gladid)->lvl; //roughtly (200+lvl*25)*0.2 (20% avg life at given lvl)
        
        (g+gladid)->hp += recovered;
        if ((g+gladid)->hp > (g+gladid)->maxhp)
            (g+gladid)->hp = (g+gladid)->maxhp;
        (g+gladid)->ap += recovered;
        if ((g+gladid)->ap > (g+gladid)->maxap)
            (g+gladid)->ap = (g+gladid)->maxap;
        
        (g+gladid)->up += POINTS_LVL_UP;
        (g+gladid)->lvl++;
        (g+gladid)->xp -= round(tonext);
    }
}

void addBuff(int id, int code, float timeleft, float value){
    if (code == BUFF_BURN){
        //caso ja tenha um burn, recumeça o tempo, e adiciona o dano que faltava
        if ((g+id)->buffs[code].timeleft > 0){
            float remaining = (g+id)->buffs[code].value * (g+id)->buffs[code].timeleft / timeInterval;
            (g+id)->buffs[code].value = value + remaining / timeleft * timeInterval;
        }
        else
            (g+id)->buffs[code].value = value;
    }
    else if (code == BUFF_MOVEMENT){
        //o valor do buff novo substitui o do anterior
        (g+id)->buffs[code].value = value;
    }
    else if (code == BUFF_RESIST){
        (g+id)->buffs[code].value = value;
    }
    (g+id)->buffs[code].timeleft = timeleft;
}

void dealDamage(int gladid, int id, float value){
    //se estiver invisivel, causa stun
    if ((g+gladid)->buffs[BUFF_INVISIBLE].timeleft > 0){
        addBuff(id, BUFF_STUN, 1.5 , 0);
    }
    
    //caso a vitima tenha o buff de resistencia e esteja virado para o atacante, leva menos dano
    if ((g+id)->buffs[BUFF_RESIST].timeleft > 0){
        if ((g+id)->head >= (g+id)->lasthitangle - (g+id)->vrad/2 && (g+id)->head <= (g+id)->lasthitangle + (g+id)->vrad/2){
            value *= (1 - (g+id)->buffs[BUFF_RESIST].value);
        }
        else{
            value *= (1 - (g+id)->buffs[BUFF_RESIST].value / 2);
        }
    }
    
    (g+id)->lasthittime = (g+id)->time;
    (g+id)->lasthitnotification = 1;
    (g+id)->hp -= value;
    if ((g+id)->hp < 0.01) //previne que hp tipo 0.000001 passe como vivo
        (g+id)->hp = 0;
}

void updateBuffs(int gladid){
    int i,j;
    for (i=0 ; i<N_BUFFS ; i++){
        if ((g+gladid)->buffs[i].timeleft > 0){
            (g+gladid)->buffs[i].timeleft -= timeInterval;

            if (i == BUFF_BURN){
                (g+gladid)->hp -= (g+gladid)->buffs[i].value * (1 - (g+gladid)->buffs[BUFF_RESIST].value/2);
                if ((g+gladid)->hp < 0.01) //previne que hp tipo 0.000001 passe como vivo
                    (g+gladid)->hp = 0;
            }
            //se atacar perde o buff
            else if (i == BUFF_INVISIBLE){
                if ((g+gladid)->action != ACTION_WAITING && (g+gladid)->action != ACTION_MOVEMENT && (g+gladid)->action != ABILITY_AMBUSH && (g+gladid)->action != ACTION_NONE && (g+gladid)->buffs[i].timeleft > 1/(g+gladid)->as/2){
                    //esse tempo é para que o gladiador perca a inbisibilidade somente depois de dar o ataque
                    (g+gladid)->buffs[i].timeleft = 1/(g+gladid)->as/2;
                }
            }
        }
        else {
            (g+gladid)->buffs[i].value = 0;
            (g+gladid)->buffs[i].timeleft = 0;
        }
    }
}

void launchProjectile(int gladid, float x, float y, float dmg, float spdx, float spdy, int type){
    struct projectile *t = (struct projectile*)malloc(sizeof(struct projectile));

    t->id = rand()%9999999;
    t->type = type;
    t->x = x;
    t->y = y;
    t->head = getAngleFromAB(x, y, x + spdx, y + spdy);
    t->spdx = spdx;
    t->spdy = spdy;
    t->dmg = dmg;
    t->dist = 0;
    t->next = NULL;
    t->owner = gladid;

    if (p == NULL)
        p = t;
    else{
        struct projectile *a = p;
        while (a->next != NULL)
            a = a->next;
        a->next = t;
    }
}

void removeProjectile(struct projectile *a){
    struct projectile *t = p;
    if (p == a){
        p = p->next;
        free(t);
    }
    else{
        while(t->next != a)
            t = t->next;
        t->next = t->next->next;
        free(a);
    }
}

void updateProjectiles(){
    int j, k;
    float hitbox = 1;
    float travelunit = 3; //em quantas etapas quebra 1 passo
    struct projectile *a = p;
    int i=0;
    //varre a lista de projeteis
    while (a != NULL){
        i++;
        int hitglad = 0;
        //numero de intervalos que um projetil anda num mesmo intervalo de tempo do gladiador
        for (k=0 ; k < travelunit ; k++){
            a->x += a->spdx / travelunit;
            a->y += a->spdy / travelunit;
            a->dist += getDistAB(0, 0, a->spdx, a->spdy) / travelunit;
            float xl = a->x - hitbox/2;
            float xr = a->x + hitbox/2;
            float yl = a->y - hitbox/2;
            float yr = a->y + hitbox/2;
            for (j=0 ; j<nglad ; j++){
                if ( (g+j)->hp > 0 && j != a->owner){
                    float xg = (g+j)->x;
                    float yg = (g+j)->y;
                    //acertou
                    if ( xg >= xl && xg <= xr && yg >= yl && yg <= yr ){
                        (g+j)->lasthitangle = getNormalAngle(getAngleFromAB((g+j)->x, (g+j)->y, a->x - a->spdx / travelunit, a->y - a->spdy / travelunit));
                        setXp(a->owner, a->dmg, j);
                        dealDamage(a->owner, j, a->dmg);
                        hitglad = 1;
                        break;
                    }
                }
            }
            if (hitglad)
                break;
        }
        
        if (a->dist >= PROJECTILE_MAX_DISTANCE || hitglad){
            //causa burn da fireball
            if (a->type == PROJECTILE_TYPE_FIREBALL){
                int m;
                for (m=0 ; m<nglad ; m++){
                    float dx = (g+m)->x - a->x;
                    float dy = (g+m)->y - a->y;
                    float dist = sqrt( pow(dx,2) + pow(dy,2) );
                    if (dist <= 2){
                        float dmg = (1-(dist/2)) * a->dmg * 3.3333; //dano = 0.6*sdmg, burn= 3.3333*sdmg = 2.00/0.6=3.3333
                        addBuff(m, BUFF_BURN, 4, dmg / 4 * timeInterval); //4 segundos
                        setXp(a->owner, dmg, m); //xp pelo burn
                        (g+m)->lasthitangle = getNormalAngle(getAngleFromAB((g+m)->x, (g+m)->y, a->x - a->spdx / travelunit, a->y - a->spdy / travelunit));
                    }
                }
            }
            else if (hitglad && a->type == PROJECTILE_TYPE_STUN){
                addBuff(j, BUFF_STUN, 1.5 , 0);
            }
            
            struct projectile *t = a->next;
            removeProjectile(a);
            a = t;
            
        }
        else
            a = a->next;
    }
}

//grava no arquivo de saida o json da diff da etapa anterior pra atual
void recordSteps(){
    char resp[6000*nglad];

    int i,j;
    char buffer[1000], buffs[300];
    float simtime;
    for (i=0 ; i<nglad ; i++){
        if ((g+i)->hp > 0){
            simtime = (g+i)->time;
            break;
        }
    }
    if (simtime == timeInterval){
        sprintf(resp, "{\"simtime\":%.1f,\"glads\":[",simtime);
        for (i=0 ; i<nglad ; i++){
            sprintf(buffs,"\"buffs\":{\"burn\":{\"value\":%.2f,\"timeleft\":%.1f},\"movement\":{\"value\":%.2f,\"timeleft\":%.1f},\"resist\":{\"value\":%.2f,\"timeleft\":%.1f},\"invisible\":{\"value\":%.2f,\"timeleft\":%.1f},\"stun\":{\"value\":%.2f,\"timeleft\":%.1f}}",
                (g+i)->buffs[BUFF_BURN].value,(g+i)->buffs[BUFF_BURN].timeleft,
                (g+i)->buffs[BUFF_MOVEMENT].value,(g+i)->buffs[BUFF_MOVEMENT].timeleft,
                (g+i)->buffs[BUFF_RESIST].value,(g+i)->buffs[BUFF_RESIST].timeleft,
                (g+i)->buffs[BUFF_INVISIBLE].value,(g+i)->buffs[BUFF_INVISIBLE].timeleft,
                (g+i)->buffs[BUFF_STUN].value,(g+i)->buffs[BUFF_STUN].timeleft
            );
            sprintf(buffer, "{\"name\":\"%s\",\"user\":\"%s\",\"id\":%i,\"lvl\":%i,\"tonext\":%i,\"xp\":%i,\"STR\":%i,\"AGI\":%i,\"INT\":%i,\"spd\":%.2f,\"as\":%.2f,\"cs\":%.2f,\"x\":%.2f,\"y\":%.2f,\"head\":%.1f,\"lockedfor\":%.2f,\"hp\":%.2f,\"maxhp\":%.2f,\"ap\":%.2f,\"maxap\":%.2f,\"items\":[%i,%i,%i,%i],\"action\":%i,\"message\":\"%s\",\"code\":\"%s\",%s}",
                (g+i)->name,
                (g+i)->user,
                i, //thread num
                (g+i)->lvl, //lvl
                getXpToNextLvl(i),
                (g+i)->xp, //xp
                (g+i)->STR, //STR
                (g+i)->AGI, //AGI
                (g+i)->INT, //INT
                (g+i)->spd, //movement speed
                (g+i)->as, //attack speed
                (g+i)->cs, //cast speed
                (g+i)->x, //X
                (g+i)->y, //Y
                (g+i)->head, //heading (0-359.9)
                (g+i)->lockedfor, //time until can act again
                (g+i)->hp, //life
                (g+i)->maxhp, //maximum life
                (g+i)->ap, //ability points
                (g+i)->maxap, //maximum ap
                (g+i)->items[0],
                (g+i)->items[1],
                (g+i)->items[2],
                (g+i)->items[3],
                (g+i)->action,
                (g+i)->message,
                (g+i)->code_exec,
                buffs
            );
            if (i!=0)
                strcat(resp,",");
            strcat(resp, buffer);	
        }
        sprintf(resp, "%s],\"projectiles\":[", resp);
        struct projectile *a = p;
        
        for (i=0 ; a != NULL ; i++){
            sprintf(buffer,"{\"x\":%.2f,\"y\":%.2f}", a->x, a->y);
            if (i!=0)
                strcat(resp,",");
            strcat(resp, buffer);	
            a = a->next;
        }
        
        sprintf(resp, "%s],\"poison\":%.2f}", resp, getDistAB(0,0,screenW/2,screenH/2));
        
    }
    else{
        sprintf(resp, ",{\"simtime\":%.1f,\"glads\":[",simtime);
        for (i=0 ; i<nglad ; i++){
            strcpy(buffer, "{");
            
            if ( (g+i)->lvl != (go+i)->lvl ){
                sprintf(buffer, "%s\"lvl\":%i,", buffer, (g+i)->lvl);
                sprintf(buffer, "%s\"tonext\":%i,", buffer, getXpToNextLvl(i));
            }
            
            if ( (g+i)->xp != (go+i)->xp ){
                sprintf(buffer, "%s\"xp\":%i,", buffer, (int)((g+i)->xp));
            }
            
            if ( (g+i)->STR != (go+i)->STR )
                sprintf(buffer, "%s\"STR\":%i,", buffer, (g+i)->STR);
            
            if ( (g+i)->AGI != (go+i)->AGI )
                sprintf(buffer, "%s\"AGI\":%i,", buffer, (g+i)->AGI);

            if ( (g+i)->INT != (go+i)->INT )
                sprintf(buffer, "%s\"INT\":%i,", buffer, (g+i)->INT);

            if ( (g+i)->spd != (go+i)->spd )
                sprintf(buffer, "%s\"spd\":%.2f,", buffer, (g+i)->spd);

            if ( (g+i)->as != (go+i)->as )
                sprintf(buffer, "%s\"as\":%.2f,", buffer, (g+i)->as);

            if ( (g+i)->cs != (go+i)->cs )
                sprintf(buffer, "%s\"cs\":%.2f,", buffer, (g+i)->cs);

            if ( (g+i)->x != (go+i)->x )
                sprintf(buffer, "%s\"x\":%.2f,", buffer, (g+i)->x);

            if ( (g+i)->y != (go+i)->y )
                sprintf(buffer, "%s\"y\":%.2f,", buffer, (g+i)->y);

            if ( (g+i)->head != (go+i)->head )
                sprintf(buffer, "%s\"head\":%.1f,", buffer, (g+i)->head);

            if ( (g+i)->lockedfor != (go+i)->lockedfor )
                sprintf(buffer, "%s\"lockedfor\":%.2f,", buffer, (g+i)->lockedfor);

            if ( (g+i)->hp != (go+i)->hp )
                sprintf(buffer, "%s\"hp\":%.2f,", buffer, (g+i)->hp);

            if ( (g+i)->maxhp != (go+i)->maxhp )
                sprintf(buffer, "%s\"maxhp\":%.2f,", buffer, (g+i)->maxhp);

            if ( (g+i)->ap != (go+i)->ap )
                sprintf(buffer, "%s\"ap\":%.2f,", buffer, (g+i)->ap);

            int s;
            for (s=0 ; s<N_SLOTS ; s++){
                if ( (g+i)->items[s] != (go+i)->items[s] ){
                    break;
                }
            }
            if (s < N_SLOTS){
                sprintf(buffer, "%s\"items\":[%i,%i,%i,%i],", buffer, (g+i)->items[0], (g+i)->items[1], (g+i)->items[2], (g+i)->items[3]);
            }

            if ( (g+i)->maxap != (go+i)->maxap )
                sprintf(buffer, "%s\"maxap\":%.2f,", buffer, (g+i)->maxap);

            if ( (g+i)->action != (go+i)->action )
                sprintf(buffer, "%s\"action\":%i,", buffer, (g+i)->action);

            if ( strcmp((g+i)->message, (go+i)->message) != 0){
                if ((g+i)->msgtype == MSG_SPEAK)
                    sprintf(buffer, "%s\"message\":\"%s\",", buffer, (g+i)->message);
                else if ((g+i)->msgtype == MSG_BREAKPOINT)
                    sprintf(buffer, "%s\"breakpoint\":\"%s\",", buffer, (g+i)->message);
            }

            if ( strcmp((g+i)->code_exec, (go+i)->code_exec) != 0){
                sprintf(buffer, "%s\"code\":\"%s\",", buffer, (g+i)->code_exec);
            }

            sprintf(buffs, "\"buffs\":{");
            
            if ( (g+i)->buffs[BUFF_BURN].timeleft != (go+i)->buffs[BUFF_BURN].timeleft )			
                sprintf(buffs, "%s\"burn\":{\"value\":%.2f,\"timeleft\":%.1f},", buffs, (g+i)->buffs[BUFF_BURN].value, (g+i)->buffs[BUFF_BURN].timeleft);
                
            if ( (g+i)->buffs[BUFF_MOVEMENT].timeleft != (go+i)->buffs[BUFF_MOVEMENT].timeleft )			
                sprintf(buffs, "%s\"movement\":{\"value\":%.2f,\"timeleft\":%.1f},", buffs, (g+i)->buffs[BUFF_MOVEMENT].value, (g+i)->buffs[BUFF_MOVEMENT].timeleft);

            if ( (g+i)->buffs[BUFF_RESIST].timeleft != (go+i)->buffs[BUFF_RESIST].timeleft )			
                sprintf(buffs, "%s\"resist\":{\"value\":%.2f,\"timeleft\":%.1f},", buffs, (g+i)->buffs[BUFF_RESIST].value, (g+i)->buffs[BUFF_RESIST].timeleft);

            if ( (g+i)->buffs[BUFF_INVISIBLE].timeleft != (go+i)->buffs[BUFF_INVISIBLE].timeleft )			
                sprintf(buffs, "%s\"invisible\":{\"value\":%.2f,\"timeleft\":%.1f},", buffs, (g+i)->buffs[BUFF_INVISIBLE].value, (g+i)->buffs[BUFF_INVISIBLE].timeleft);

            if ( (g+i)->buffs[BUFF_STUN].timeleft != (go+i)->buffs[BUFF_STUN].timeleft )			
                sprintf(buffs, "%s\"stun\":{\"value\":%.2f,\"timeleft\":%.1f}", buffs, (g+i)->buffs[BUFF_STUN].value, (g+i)->buffs[BUFF_STUN].timeleft);
            
            if ( buffs[strlen(buffs)-1] == ',' )
                buffs[strlen(buffs)-1] = '}';
            else
                strcat(buffs, "}");

            if (strcmp(buffs, "\"buffs\":{}") != 0)
                strcat(buffer, buffs);
            
            if ( buffer[strlen(buffer)-1] == ',' )
                buffer[strlen(buffer)-1] = '}';
            else
                strcat(buffer, "}");
            
            if (i!=0)
                strcat(resp,",");
            strcat(resp, buffer);	
        }
    
        sprintf(resp, "%s],\"projectiles\":[", resp);
        struct projectile *a = p;
        
        for (i=0 ; a != NULL ; i++){
            sprintf(buffer,"{\"x\":%.2f,\"y\":%.2f,\"head\":%.1f,\"id\":%i,\"type\":%i,\"owner\":%i}", a->x, a->y, a->head, a->id, a->type, a->owner);
            if (i!=0)
                strcat(resp,",");
            strcat(resp, buffer);	
            a = a->next;
        }
        
        if (simtime >= POISON_TIME){
            float startdist = getDistAB(0,0,screenW/2,screenH/2);
            float spread = (simtime - POISON_TIME) / POISON_SPEED;
            float safezone = startdist - spread;
            if (safezone < 0)
                safezone = 0;
            sprintf(resp, "%s],\"poison\":%.2f}", resp, safezone);
        }
        else
            sprintf(resp, "%s]}", resp);
    }
    
    for (i=0 ; i<nglad ; i++){
        *(go+i) = *(g+i);
    }
    
    //printf("%s\n",resp);
    fprintf(outArq, "%s",resp);
}

int checkAlive(){
    int i, nalive=0;
    for (i=0 ; i<nglad ; i++){
        if ( (g+i)->hp > 0 )
            nalive++;
        else if ((g+i)->hp < 0)
            (g+i)->hp = 0;
    }
    if (nalive <= 1){
        endsim = 1;
        recordSteps();
    }
    return nalive;
}

//verifica se todos gladiadores estao no mesmo tempo
int checkSync(){
    int i, first=1, sync=0, alive=0;
    float sample;
    for (i=0 ; i<nglad ; i++){
        if ((g+i)->hp > 0){
            if (first){
                first = 0;
                sample = (g+i)->time;
                sync++;
            }
            else if(sample == (g+i)->time){
                sync++;
            }
            alive++;
        }
    }
    
    if (sync == alive)
        return 1;
    else
        return 0;
}

//cria veneno das bordas da arena
void spread_poison(int gladid){
    float startdist = getDistAB(0,0,screenW/2,screenH/2);
    float spread = ((g+gladid)->time - POISON_TIME) / POISON_SPEED;
    
    //poison causa 5%hp/s na vida
    if ( getDistUnsafe(gladid, screenW/2, screenH/2) >= startdist - spread)
        (g+gladid)->hp -= 0.05 * (g+gladid)->maxhp * timeInterval;
}

//atualiza o counter da simulacao
int updateSimulation(int gladid){	
    int i;	
    int timeout[nglad];
    
    if (endsim)
        return 0;
    
    for (i=0 ; i<nglad ; i++)
        timeout[i] = 10000;
    
    int loweri;
    do{
        usleep(1);
        loweri = -1;
        for (i=0 ; i<nglad ; i++){
            if ((g+i)->hp > 0){
                if (loweri == -1 || (g+i)->time < (g+loweri)->time){
                    loweri = i;
                }
            }
        }
        timeout[loweri]--;
    }while(!endsim && loweri != gladid && timeout[loweri] > 0);
    
    if (timeout[loweri] == 0){
        (g+loweri)->hp = 0;
        printf("Gladiator %s timed out\n",(g+loweri)->name);
        //endsim = 1;
    }

    if ((g+gladid)->hp > 0){
        (g+gladid)->time += timeInterval;
        //printf("%i %f\n",gladid, (g+gladid)->time);
    }
        
    if (checkSync()){
        updateProjectiles();
        recordSteps();
    }
    
    if ((g+gladid)->hp <= 0){
        (g+gladid)->hp = 0;
        return 0;
    }
    else{
        pthread_mutex_lock(&lock);
        
        //recupera ap
        if ((g+gladid)->buffs[BUFF_INVISIBLE].timeleft == 0)
            (g+gladid)->ap += (AP_REC_BASE + AP_REC_INT * (g+gladid)->INT) * timeInterval;
        if ((g+gladid)->ap > (g+gladid)->maxap)
            (g+gladid)->ap = (g+gladid)->maxap;

        /*
        (g+gladid)->hp += (1 + 0.1 * (g+gladid)->STR) * timeInterval;
        if ((g+gladid)->hp > (g+gladid)->maxhp)
            (g+gladid)->hp = (g+gladid)->maxhp;
        */

        updateBuffs(gladid);

        //passa fica mais proximo de poder agir de novo
        if ((g+gladid)->lockedfor > 0)
            (g+gladid)->lockedfor -= timeInterval;

        if ((g+gladid)->msgtime > 0)
            (g+gladid)->msgtime -= timeInterval;
        else if (strcmp((g+gladid)->message, "") != 0)
            strcpy((g+gladid)->message, "");
        
        if ( (g+gladid)->time > POISON_TIME){
            spread_poison(gladid);
        }
                
        pthread_mutex_unlock(&lock);		
    }

    do{
        usleep(1);
        for (i=0 ; i<nglad ; i++){
            if ((g+gladid)->time > (g+i)->time && (g+i)->hp > 0){
                break;
            }
        }
        checkAlive();
    }while(!endsim && i < nglad);
    
    return 1;	
}

//aguarda o tempo ate o gladiador estar pronto para agir novamente
void waitForLockedStatus(int gladid){
    //updates nos projeteis, tempo e grava na arquivo, mesmo quando o gladiador nao pode agir
    
    while ( !endsim && ((g+gladid)->lockedfor > 0 || (g+gladid)->buffs[BUFF_STUN].timeleft > 0) ){
        //updateProjectiles();
        if (!updateSimulation(gladid)){
            return;
        }
        checkAlive();
        (g+gladid)->action = ACTION_WAITING;
    }
    
    //(g+gladid)->action = ACTION_NONE;
}

//as funcoes unsafe cumprem o papel das mesmas funções da API, porém sem alterar o actioncode ou trancar o gladiador.
//servem para ser usadas dentro de outras funções.

int turnToUnsafe(int gladid, float x, float y){
    float newhead = getNormalAngle(getAngleUnsafe(gladid, x, y));
    float dif = getNormalAngle(newhead - (g+gladid)->head);
    if (dif < 180){
        if (dif <= (g+gladid)->ts * timeInterval){
            (g+gladid)->head = newhead;
            return 1;
        }
        else
            (g+gladid)->head += (g+gladid)->ts * timeInterval;
    }
    else{
        if (360-dif <= (g+gladid)->ts * timeInterval){
            (g+gladid)->head = newhead;
            return 1;
        }
        else
            (g+gladid)->head -= (g+gladid)->ts * timeInterval;
    }
    (g+gladid)->head = getNormalAngle((g+gladid)->head);
    return 0;
}

//vira a visao em até um passo
float turnStepUnsafe(int gladid, float ang){
    ang = getNormalAngle(ang);
    float r;
    if (ang < 180){
        if (ang <= (g+gladid)->ts * timeInterval){
            (g+gladid)->head += ang;
            r = ang;
        }
        else{
            (g+gladid)->head += (g+gladid)->ts * timeInterval;
            r = (g+gladid)->ts * timeInterval;
        }
    }
    else{
        if (360 - ang <= (g+gladid)->ts * timeInterval){
            (g+gladid)->head += ang;
            r = ang - 360;
        }
        else{
            (g+gladid)->head -= (g+gladid)->ts * timeInterval;
            r = -((g+gladid)->ts * timeInterval);
        }
    }
    (g+gladid)->head = getNormalAngle((g+gladid)->head);
    
    return r;
}

float moveForwardUnsafe(int gladid){
    float hip = (g+gladid)->spd * timeInterval;
    if ((g+gladid)->buffs[BUFF_MOVEMENT].timeleft > 0)
        hip *= (g+gladid)->buffs[BUFF_MOVEMENT].value;

    float ang = (g+gladid)->head;
    float dx, dy;
    calcSidesFromAngleDist(&dx, &dy, hip, ang);

    float oldx = (g+gladid)->x;
    float oldy = (g+gladid)->y;
    (g+gladid)->x += dx;
    (g+gladid)->y -= dy;

    preventLeaving(gladid);
    preventCollision(gladid, dx, -dy);
    
    return getDistUnsafe(gladid, oldx, oldy);
}

int moveToUnsafe(int gladid, float x, float y){
    if (getDistUnsafe(gladid, x, y) <= 0.01){
        (g+gladid)->x = x;
        (g+gladid)->y = y;
        return 1;
    }
    if (turnToUnsafe(gladid, x, y)){
        float move = (g+gladid)->spd * timeInterval;
        if ((g+gladid)->buffs[BUFF_MOVEMENT].timeleft > 0)
            move *= (g+gladid)->buffs[BUFF_MOVEMENT].value;
            
        if (move >= getDistUnsafe(gladid, x, y)){
            (g+gladid)->x = x;
            (g+gladid)->y = y;
            return 1;
        }
        else{
            moveForwardUnsafe(gladid);
            return 0;
        }
    }
    return 0;
}

//define a posicao e direcao inicial do gladiador
void setStartingPos(int gladid){
    float centerX = screenW/2.0;
    float centerY = screenH/2.0;
    float radius = screenH/2 * 0.8;
    float dang = 360.0/nglad;
    float x,y;
    calcSidesFromAngleDist(&x, &y, radius, dang * gladid);
    (g+gladid)->x = centerX + x;
    (g+gladid)->y = centerY + y;
    float newhead = getNormalAngle(getAngleUnsafe(gladid, centerX, centerY));
    (g+gladid)->head = newhead;
}

void registerGlad(int gladid){
    (g+gladid)->vrad = VIS_RAD; //raio de visao 120 graus
    (g+gladid)->vis = VIS_RANGE; //alcance da visao 9 passos
    (g+gladid)->lockedfor = 0;
    setStartingPos(gladid); //inicializa a posicao inicial de cada gladiador formando um circulo
    (g+gladid)->targetlocked = 0; //nenhum alvo fixado
    (g+gladid)->targetsaved = 0; //nenhum alvo fixado
    (g+gladid)->moveLock = 0; //nao esta tentando nenhum movimento longo
    (g+gladid)->up = 0; //quantos pontos faltam distribuir
    (g+gladid)->xp = 0; //experiencia
    (g+gladid)->lasthitangle = 0; //angulo de onde veio o ultimo ataque
    (g+gladid)->lasthitnotification = 0; //sem notificacao de acerto
    (g+gladid)->lasthittime = -999;
    (g+gladid)->action = ACTION_NONE; //action inicial do gladiador
    strcpy((g+gladid)->message, "");
    strcpy((g+gladid)->code_exec, "");
    (g+gladid)->msgtime = 0;
    (g+gladid)->msgtype = MSG_SPEAK;

    int i;
    for (i=0 ; i<N_BUFFS ; i++){ //zera todos buffs
        (g+gladid)->buffs[i].timeleft = 0;
        (g+gladid)->buffs[i].value = 0;
    }

    for (i=0 ; i<N_SLOTS ; i++){ //zera todos slots de item
        (g+gladid)->items[i] = 0;
    }
}

int createGladiator(int port){
    int i, gladid;

    //pthread_mutex_lock(&lock);
    if (g == NULL){
        g = (struct gladiador*)malloc(sizeof(struct gladiador) * nglad);
        go = (struct gladiador*)malloc(sizeof(struct gladiador) * nglad);
        for (i=0 ; i<nglad ; i++)
            (g+i)->port = 0;
    }
    //pthread_mutex_unlock(&lock);	
    
    for (i=0 ; i<nglad ; i++){
        if ( (g+i)->port == 0 ){
            (g+i)->port = port;
            (g+i)->lvl = 0; //indica que o gladiador ainda está em preparo (setup)
            gladid = i;
            break;
        }
    }
    
    registerGlad(gladid);
    
    return gladid;
}

//calcula o custo do ponto de atributo
int calcCost(int val){
    if (val == 0)
        return 0;
    return ceil((float)val/6) + calcCost(val-1);
}

//verifica se distribuiu os 50 pontos corretamente
int checkSetup(int gladid){
    int sum = calcCost((g+gladid)->STR) + calcCost((g+gladid)->AGI) + calcCost((g+gladid)->INT);
    if (sum != 50)
        return 0;
    if ((g+gladid)->STR < 0)
        return 0;
    if ((g+gladid)->AGI < 0)
        return 0;
    if ((g+gladid)->INT < 0)
        return 0;

    (g+gladid)->lvl = 1; //lvl inicial

    return 1;
}

int getLockedTarget(int gladid){
    int i;
    for (i=0 ; i<nglad ; i++){
        if ((g+i)->port == (g+gladid)->targetlocked)
            return i;
    }
    return -1;
}

int isVisible(int gladid, int target){
    float dist = getDistUnsafe(gladid, (g+target)->x, (g+target)->y);
    float ang = getNormalAngle(getAngleUnsafe(gladid, (g+target)->x, (g+target)->y) - (g+gladid)->head);
    if ( dist <= (g+gladid)->vis && (ang <= (g+gladid)->vrad/2 || ang >= 360-(g+gladid)->vrad/2) && (g+target)->buffs[BUFF_INVISIBLE].timeleft <= 0 )
        return 1;
    else
        return 0;
}

void attackMeleeUnsafe(int gladid, float bonusdmg){
    int i;
    for (i=0 ; i<nglad ; i++){
        if (i != gladid && (g+i)->hp > 0){
            float dist = getDistUnsafe(gladid, (g+i)->x, (g+i)->y);
            float ang = getNormalAngle(getAngleUnsafe(gladid, (g+i)->x, (g+i)->y) - (g+gladid)->head);
            if ( dist <= 2 && (ang <= 90 || ang >= 270) ){ //180g de raio de ataque
                (g+i)->lasthitangle = getNormalAngle(getAngleFromAB((g+i)->x, (g+i)->y, (g+gladid)->x, (g+gladid)->y));
                float dmg = (g+gladid)->mdmg * bonusdmg;
                setXp(gladid, dmg, i);
                dealDamage(gladid, i, dmg);
            }
        }
    }
}

int isLockedTargetVisibleUnsafe(int gladid){
    int target = getLockedTarget(gladid);
    if (target != -1 && isVisible(gladid, target))
        return 1;
    else{
        (g+gladid)->targetlocked = 0;
        return 0;
    }
}

void increaseHighAttr(int *m[3], int v){
    int *a = NULL, *b = NULL, *n = NULL;
    
    if (*m[0] > *m[1] && *m[0] > *m[2]){
        n = m[0];
    }
    else if (*m[1] > *m[0] && *m[1] > *m[2]){
        n = m[1];
    }
    else if (*m[2] > *m[0] && *m[2] > *m[1]){
        n = m[2];
    }
    else if (*m[0] < *m[1] && *m[0] < *m[2]){
        a = m[1];
        b = m[2];
    }
    else if (*m[1] < *m[0] && *m[1] < *m[2]){
        a = m[0];
        b = m[2];
    }
    else if (*m[2] < *m[0] && *m[2] < *m[1]){
        a = m[0];
        b = m[1];
    }
    
    if (!n && !a && !b){
        *m[rand()%3] += v;
    }
    else if (!n){
        if (rand()%2){
            *a += v;
        }
        else {
            *b += v;
        }
    }
    else{
        *n += v;
    }
}

void increaseLowAttr(int *m[3], int v){
    int *a = NULL, *b = NULL, *n = NULL;

    if (*m[0] < *m[1] && *m[0] < *m[2]){
        n = m[0];
    }
    else if (*m[1] < *m[0] && *m[1] < *m[2]){
        n = m[1];
    }
    else if (*m[2] < *m[0] && *m[2] < *m[1]){
        n = m[2];
    }
    else if (*m[0] > *m[1] && *m[0] > *m[2]){
        a = m[1];
        b = m[2];
    }
    else if (*m[1] > *m[0] && *m[1] > *m[2]){
        a = m[0];
        b = m[2];
    }
    else if (*m[2] > *m[0] && *m[2] > *m[1]){
        a = m[0];
        b = m[1];
    }
    
    if (!n && !a && !b){
        *m[rand()%3] += v;
    }
    else if (!n){
        if (rand()%2){
            *a += v;
        }
        else {
            *b += v;
        }
    }
    else{
        *n += v;
    }
}

void explodeItemName(char *item, char *name, int *lvl){
    char *dash = strstr(item, "-");
    strcpy(name, dash+1);
    dash = strstr(name, "-");
    *lvl = atoi(dash+1);
    *dash = '\0';
}

int itemEffect(int gladid, char *item){
    char name[20];
    int lvl;

    explodeItemName(item, name, &lvl);

    if (lvl > 0){
        if (strcmp(name, "hp") == 0 && lvl <= 5){
            (g+gladid)->hp += lvl*20 + lvl*2 * (g+gladid)->lvl;
            (g+gladid)->hp = (g+gladid)->hp > (g+gladid)->maxhp ? (g+gladid)->maxhp : (g+gladid)->hp;
        }
        else if (strcmp(name, "ap") == 0 && lvl <= 5){
            (g+gladid)->ap += lvl*20 + lvl*2 * (g+gladid)->lvl;
            (g+gladid)->ap = (g+gladid)->ap > (g+gladid)->maxap ? (g+gladid)->maxap : (g+gladid)->ap;
        }
        else if (strcmp(name, "high") == 0 && lvl <= 4){
            int highArray[4] = {10, 5, 3, 2};
            int points = lvl + ceil((g+gladid)->lvl / highArray[lvl-1]);
            int *m[3] = { &((g+gladid)->STR), &((g+gladid)->AGI), &((g+gladid)->INT) };
            increaseHighAttr(m, points);
        }
        else if (strcmp(name, "low") == 0 && lvl <= 4){
            int lowArray[4] = {10, 5, 3, 2};
            int points = 1+lvl + ceil((g+gladid)->lvl / lowArray[lvl-1]);
            int *m[3] = { &((g+gladid)->STR), &((g+gladid)->AGI), &((g+gladid)->INT) };
            increaseLowAttr(m, points);
        }
        else if (strcmp(name, "xp") == 0 && lvl <= 3){
            (g+gladid)->xp += (getXpToNextLvl(gladid) - (g+gladid)->xp) * 0.25 * lvl;
        }
        else{
            return 0;
        }
    }
    else{
        return 0;
    }

    return 1;
}

void appendCode(int gladid, char *format, ...){
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

    sprintf((g+gladid)->code_exec, "%s", message);
    
}