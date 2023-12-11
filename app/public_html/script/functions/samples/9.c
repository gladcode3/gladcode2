float v = 1;
loop(){
    while(getX() != v || getY() != v){
        turnToAngle(getAngle(v,v));
        stepForward();
    }
    if (v == 1)
        v = 24;
    else
        v = 1;
}