loop(){
    if (getCloseEnemy()){
        if (isProtected() || getDistToTarget() < 2)
            stepBack();
        else
            attackRanged(getTargetX(), getTargetY());
    }
    else
        turn(50);
}