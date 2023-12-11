loop(){
    if (getCloseEnemy()){
        if (isRunning() || getDistToTarget() < 2)
            teleport(0,0);
        else
            fireball(getTargetX(), getTargetY());
    }
    else if (!getHit()){
        turn(5);
        stepForward();
    }
    else
        turnToLastHit();
}