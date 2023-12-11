dx = 20, dy = 5
def loop():
    global dx, dy
    if getX() == 20 and getY() == 20:
        dx = 5
    if getX() == 5 and getY() == 5:
        dx = 20
    if getX() == 20 and getY() == 5:
        dy = 20
    if getX() == 5 and getY() == 20:
        dy = 5
    moveTo(dx, dy)
 