setup(){
    setName("Rouge");
    setSTR(8);
    setAGI(18);
    setINT(4);
    setSpritesheet("f0d413c1e3612da663b6b5b82f994c0d");
}

int start = 1;

loop(){
	upgradeAGI(1);
	if (getLastHitTime() < 1 && getAmbushTimeLeft() == 0){
		ambush();
		turnToAngle(getLastHitAngle());
	}
	else if (getCloseEnemy() && isSafeHere()){
		if(doYouSeeMe()){
			if (getAmbushTimeLeft() <= 1){
				attackRanged(getTargetX(), getTargetY());
				if (isStunned() && getAp() > 90)
					assassinate(getTargetX(), getTargetY());
				if (getAp() > 60)
					ambush();
			}
			else{
				turnToTarget();
				if (getDist(getTargetX(), getTargetY()) < 5)
					stepBack();
				else
					stepLeft();
			}
		}
		else{
			if (getAmbushTimeLeft() > 0){
				turnToTarget();
				stepLeft();
				if (getAp() < 30){
					if(getAmbushTimeLeft() <= 1)
						attackRanged(getTargetX(), getTargetY());
				}
				else{
					if (getAmbushTimeLeft() <= 1.5){
						attackRanged(getTargetX(), getTargetY());
						if (getDist(getTargetX(), getTargetY()) > 2){
							if (isStunned() && getAp() > 80)
								assassinate(getTargetX(), getTargetY());
							else
								attackRanged(getTargetX(), getTargetY());
						}
					}
				}
				if (getDist(getTargetX(), getTargetY()) < 2){
					turnToTarget();
					stepBack();
				}
			}
			else if (getAp() > 80)
				assassinate(getTargetX(), getTargetY());
			else if (getDist(getTargetX(), getTargetY()) < 5)
				stepBack();
			else
				attackRanged(getTargetX(), getTargetY());
		}
		
	}
	else if (start){
		if(moveTo(9,9)){
			start = 0;
			while(!turnTo(12.5,12.5));
		}
	}
	else{
		if (!isSafeHere()){
			int i;
			for (i=0 ; i<10 ; i++){
				moveTo(12.5,12.5);
				if (getCloseEnemy() && getAmbushTimeLeft() == 0)
					ambush();
			}
		}
		else
			turn(50);
	}
}