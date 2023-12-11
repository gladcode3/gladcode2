setup(){
    setName("Warrior");
    setSTR(20);
    setAGI(4);
    setINT(2);
    setSpritesheet("8daec5aa3f0cef2dfe15099a8ded0714");
}

int start = 1;
float hp;

loop(){
	upgradeSTR(1);
	int hit = 0;
	if (hp != getHp()){
		hp = getHp();
		hit = 1;
	}

	if (hit){
		if (getBlockTimeLeft() < 1.5)
			block();
		turnToAngle(getLastHitAngle());
	}

	if (getHighHp()){
		charge();
	}
	else if (!start)
		turn(50);
	else if (start){
		if(moveTo(12.5,12.5))
			start = 0;
	}
}
	