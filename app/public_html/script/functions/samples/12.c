loop(){
    if (getLowHp()){
        if (getSpeed() > getTargetSpeed()){
            if(getDist(getTargetX(), getTargetY()) <= 1)
                attackMelee();
            else
                moveToTarget();
        }
        else
            attackRanged(getTargetX(), getTargetY());
    }
    else
        turnLeft(5);
}