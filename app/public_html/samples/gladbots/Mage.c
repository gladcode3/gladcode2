setup(){
    setName("Mage");
    setSTR(6);
    setAGI(0);
    setINT(20);
    setSpritesheet("cc35b0addb0210cd1392849df86ced9c");
}

loop(){
	upgradeINT(1);
	if(getLowHp()){
		if (getAp() >= 40)
			fireball(getTargetX(), getTargetY());
		else
			attackRanged(getTargetX(), getTargetY());
	}
	else{
		while(getX() != 12.5 || getY() != 12.5)
			teleport(12.5,12.5);
		turn(50);
	}

}
	