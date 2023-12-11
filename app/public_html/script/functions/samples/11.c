loop(){
    if (getLastHitTime() <= 2.0)
        stepLeft();
    else if (getCloseEnemy())
        attackRanged(getTargetX(), getTargetY());
    else
        turnLeft(5);
}