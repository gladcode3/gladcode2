v = 1
def loop():
    global v
    while getX() != v or getY() != v:
        turnToAngle(getAngle(v,v))
        stepForward()
     
    if v == 1:
        v = 24
    else:
        v = 1
 