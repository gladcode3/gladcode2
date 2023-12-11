loop(){
    if (getHit()){
        turnToAngle(getLastHitAngle());
        getCloseEnemy();

        if(isTargetVisible())
            attackRanged(getTargetX(), getTargetY());
    }
}