loop(){
    if (getLastHitTime() <= 2)
        stepLeft();
    else if (getCloseEnemy())
        attackRanged(getTargetX(), getTargetY());
    else
        turnLeft(5);
}