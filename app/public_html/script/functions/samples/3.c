loop(){
    if (getLowHp()){
        if (getHp() > 20)
            attackRanged(getTargetX(), getTargetY());
        else
            stepBack();
    }
    else
        turnRight(5);
}