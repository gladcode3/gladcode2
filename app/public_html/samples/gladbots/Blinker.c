setup(){
    setName("Blinker");
    setSTR(6);
    setAGI(0);
    setINT(20);
    setSpritesheet("60b785096ebc6019a0de35ed6ad44662");
}

float x = 5, y = 5;

vai(){
	while(getX() != x || getY() != y)
		teleport(x,y);
}

loop(){
	vai();
	x = 20;
	vai();
	y = 20;
	vai();
	x = 5;
	vai();
	y = 5;
}
	