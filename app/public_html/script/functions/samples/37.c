loop(){
    if (getHit()){
        speak("Aieeee");
        ambush();
        turnToLastHit();
    }
    else if (getCloseEnemy()){
        attackRanged(getTargetX(), getTargetY());
        speak("Te achei em X:%.1f Y:%.1f", getTargetX(), getTargetY());
    }
    else{
        if(getX() == 12.5 && getY() == 12.5){
            turn(30);
            speak("Roda roda roda...");
        }
        else
            moveTo(12.5,12.5);
    }
}