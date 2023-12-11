int start = 1;

loop(){
    if (start){
        while(!moveTo(7.5,12.5));
        turnTo(12.5,12.5);
        start = 0;
    }
    moveForward(10);
    turn(180);
}