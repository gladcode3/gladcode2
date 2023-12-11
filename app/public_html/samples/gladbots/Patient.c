setup(){
    setName("Patient");
    setSTR(16);
    setAGI(12);
    setINT(2);
    setSpritesheet("5eddb402bf6ec2094dccd33b167b6356");
}

int start = 1;

loop(){
	upgradeSTR(1);
	if (getCloseEnemy()){
		float dist = getDist(getTargetX(), getTargetY());
		if (dist < 0.8 && isTargetVisible()){
			attackMelee();
		}
		else
			moveToTarget();
	}
	else{
		if (start){
			if(moveTo(12.5,12.5))
				start = 0;
		}
		else
			turnLeft(50);
	}
}