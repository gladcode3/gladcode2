float r = 1;
loop(){
    while(isSafeHere() && !moveTo(r,r));
    while(isSafeHere() && !moveTo(25-r,r));
    while(isSafeHere() && !moveTo(25-r,25-r));
    while(isSafeHere() && !moveTo(r,25-r));
    while (getDist(12.5,12.5) >= getSafeRadius() - 2)
        moveTo(12.5,12.5);
    r = 12.5 - getSafeRadius()/2;
}