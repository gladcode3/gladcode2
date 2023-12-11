loop(){
    if (getHit()){
        turnToLastHit();
        if (howManyEnemies() == 1 && getLowHp() && getTargetHealth() <= 0.3)
            attackRanged(getTargetX(), getTargetY());
        else{
            while (howManyEnemies() > 1)
                stepBack();
        }
    }
    else{
        while (!getHit() && !moveTo(20,5));
        while (!getHit() && !moveTo(5,20));
    }
}