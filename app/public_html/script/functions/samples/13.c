loop(){
    if (getLastHitTime() <= 2.0)
        stepLeft();
    else if (getFarEnemy())
        attackRanged(getTargetX(), getTargetY());
    else
        turnLeft(5);
}