loop(){
    if (getHit()){
        turnToLastHit();
        if (getCloseEnemy())
            attackRanged(getTargetX(), getTargetY());
        else{
            float diff, st = getSimTime();
            do{
                diff = getSimTime() - st;
                stepBack();
            }while(diff < 4 && howManyEnemies() == 0);
        }
    }
    else if (getCloseEnemy())
        attackRanged(getTargetX(), getTargetY());
    else if (moveTo(12.5, 12.5))
        turn(45);
}