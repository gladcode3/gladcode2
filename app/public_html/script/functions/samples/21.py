r = 1
def loop():
    global r
    while isSafeHere() and not moveTo(r,r):
        pass
    while isSafeHere() and not moveTo(25-r,r):
        pass
    while isSafeHere() and not moveTo(25-r,25-r):
        pass
    while isSafeHere() and not moveTo(r,25-r):
        pass
    while getDist(12.5,12.5) >= getSafeRadius() - 2:
        moveTo(12.5,12.5)
    r = 12.5 - getSafeRadius()/2
 