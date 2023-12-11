float x,y;
loop(){
    
    if ((getX() == x && getY() == y) || !isSafeThere(x,y)){
        x = (rand()%250)/10;
        y = (rand()%250)/10;
    }
    
    if(isSafeThere(x,y)){
        if (getAp() > 70 && getDist(x,y) > 2)
            teleport(x,y);
        else
            moveTo(x,y);
    }
}