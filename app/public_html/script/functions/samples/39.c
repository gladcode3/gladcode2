int centro = 0;
loop(){
    if (!centro){
        while(!moveTo(12.5,12.5));
        centro = 1;
    }
    turnLeft(5);
    stepForward();
}