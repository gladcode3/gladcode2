setup(){
    setName("Archie");
    setSTR(10);
    setAGI(18);
    setINT(0);
    setSpritesheet("3b5dc21ff059beae0d8fc65f5ab16e0c");
}

int f = 0;

loop(){
	upgradeAGI(1);
	if (!getCloseEnemy()){
		if (f){
			if (getDist(12.5,12.5) > 5)
				f = 0;
			else
			turnRight(100);
		}
		else if (moveTo(12.5,12.5))
			f = 1;
	}
	else{
		if (getDist(getTargetX(), getTargetY()) < 2){
			if (getDist(12.5,12.5) > 10){
				turnTo(getTargetX(), getTargetY());
				stepLeft();
			}
			else
				stepBack();
		}
		else
			attackRanged(getTargetX(), getTargetY());
	}
}