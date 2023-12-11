setup(){
    setName("Magnus");
    setSTR(8);
    setAGI(4);
    setINT(18);
    setSpritesheet("d2eb1d688b45e19129d2d25959170ad9");
}

loop(){
	upgradeINT(1);
	if (getCloseEnemy() && getDist(getTargetX(), getTargetY()) < 5){
		float dx,dy;
		float safe = sqrt(pow(getSafeRadius(), 2)/2);
		float x = getX() - getTargetX();
		if (x < 0)
			dx = 12.5 + safe - 1;
		else
			dx = 12.5 - safe + 1;
		float y = getY() - getTargetY();
		if (y < 0)
			dy = 12.5 + safe - 1;
		else
			dy = 12.5 - safe + 1;
		teleport(dx,dy);
	}
	else if(getLowHp()){
		if (getAp() >= 100)
			fireball(getTargetX(), getTargetY());
		else
			attackRanged(getTargetX(), getTargetY());
	}
	else if (getLastHitTime() < 1){
		turnToAngle(getLastHitAngle());
	}
	else{
		if (getX() != 12.5 || getY() != 12.5)
			moveTo(12.5,12.5);
		else 
			turn(50);
	}

}
	