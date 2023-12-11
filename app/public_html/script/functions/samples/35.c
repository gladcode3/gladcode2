int volta = 0;
loop(){
    if (!volta){
        if (moveTo(23,12))
            volta = 1;
    }
    else{
        while(!moveTo(2,12));
        volta = 0;
    }
}