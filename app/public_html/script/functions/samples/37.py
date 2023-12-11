def loop():
    if getHit():
        speak("Aieeee")
        ambush()
        turnToLastHit()
     
    elif getCloseEnemy():
        attackRanged(getTargetX(), getTargetY())
        speak("Te achei em X:{} Y:{}".format(getTargetX(), getTargetY()))
     
    else:
        if getX() == 12.5 and getY() == 12.5:
            turn(30)
            speak("Roda roda roda...")
         
        else:
            moveTo(12.5,12.5)
     
 