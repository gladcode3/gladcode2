int start = 1;
loop(){
    if (getLowHp()){
        float d = getDistToTarget();
        if (!isSlowed() && d > 2)
            charge();
        else if (d <= 1)
            attackMelee();
        else
            moveToTarget();
    }
    else if (start || !isSafeHere()){
        if(moveTo(12.5,12.5))
            start = 0;
    }
    else
        turn(5);
}