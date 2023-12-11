float dx = 20, dy = 5;
loop(){
    if (getX() == 20 && getY() == 20)
        dx = 5;
    if (getX() == 5 && getY() == 5)
        dx = 20;
    if (getX() == 20 && getY() == 5)
        dy = 20;
    if (getX() == 5 && getY() == 20)
        dy = 5;
    moveTo(dx, dy);
}