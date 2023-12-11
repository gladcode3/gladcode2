centro = False
def loop():
    global centro
    if not centro:
        while not moveTo(12.5,12.5):
            pass
        centro = True
     
    turnLeft(5)
    stepForward()
 