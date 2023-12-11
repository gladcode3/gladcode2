upgrade(){
    if (getLvl() < 5)
        upgradeSTR(5);
    else
        upgradeINT(5);
}