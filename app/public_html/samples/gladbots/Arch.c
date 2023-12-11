setup(){
    setName("Arch");
    setSTR(6);
    setAGI(20);
    setINT(0);
    setSpritesheet("9b48a8dbc1130be4959d9985c893255f");
}

int f = 0;

loop(){
	upgradeAGI(1);
	if (!getCloseEnemy()){
		if (f)
			turnRight(100);
		else if (moveTo(12.5,12.5))
			f = 1;
	}
	else
		attackRanged(getTargetX(), getTargetY());
}