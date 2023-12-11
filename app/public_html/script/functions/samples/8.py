def loop():
    if getSTR() >= getAGI():
        upgradeAGI(5)
    elif getINT() >= getSTR():
        upgradeSTR(5)
    else:
        upgradeINT(5)
    while not moveTo(5, 20):
        pass
    while not moveTo(20, 5):
        pass