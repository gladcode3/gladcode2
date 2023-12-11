x = 0, y = 0
def loop():
    global x, y
    if (getX() == x and getY() == y) or not isSafeThere(x,y):
        x = randint(0,250)/10
        y = randint(0,250)/10
     
    if isSafeThere(x,y):
        if getAp() > 70 and getDist(x,y) > 2:
            teleport(x,y)
        else:
            moveTo(x,y)
     
 