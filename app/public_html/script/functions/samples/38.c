int centro = 0;
loop(){
    if (!centro){
        while(!moveTo(12.5,12.5));
        centro = 1;
    }
    turnRight(5);
    stepBack();
}