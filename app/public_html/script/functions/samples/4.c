int start = 1;
loop(){
    if (start){
        if(moveTo(12.5,12.5))
            start = 0;
    }
    if(getLowHp()){
        if (doYouSeeMe() && getBlockTimeLeft() <= 0)
            block();
        else
            charge();
    }
    else if (!start)
        turn(50);
}