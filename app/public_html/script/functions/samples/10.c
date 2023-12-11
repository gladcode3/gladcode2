int start = 1;
loop(){
    if (start){
        if(moveTo(12.5,12.5))
            start = 0;
    }
    else if (getHit()){
        if (getBurnTimeLeft() > 0)
            teleport(0,0);
        else{
            ambush();
            turnToLastHit();
        }
    }
    else if (!start)
        turn(50);
}