loop(){
    if (getSTR() >= getAGI())
        upgradeAGI(5);
    else if (getINT() >= getSTR())
        upgradeSTR(5);
    else
        upgradeINT(5);
    while(!moveTo(5,20));
    while(!moveTo(20,5));
}