loop() {
    if (getHit() && getHp() < 100){
        if (isItemReady("pot-hp-3")){
            useItem("pot-hp-3");
        }
        else{
            teleport(25,25);
        }
    }
    else if (getCloseEnemy()){
        if (getAp() < 200 && isItemReady("pot-ap-3")){
            useItem("pot-ap-3");
        }

        fireball(getTargetX(), getTargetY());
    }
    else if (getLvl() > 8 && isItemReady("pot-xp-2")){
        useItem("pot-xp-2");
    }
    else if (getINT() > getSTR() && getINT() > getAGI() && isItemReady("pot-high-1")){
        useItem("pot-high-1");
    }
    else if (isItemReady("pot-low-1")){
        useItem("pot-low-1");
    }
}