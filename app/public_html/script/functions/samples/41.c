loop(){
    if (getLastHitTime() <= 2)
        stepRight();
    else if (getCloseEnemy())
        attackRanged(getTargetX(), getTargetY());
    else
        turnRight(5);
}