volta = False
def loop():
    global volta
    if not volta:
        if moveTo(23,12):
            volta = True
     
    else:
        while not moveTo(2,12):
            pass
        volta = False
     
 