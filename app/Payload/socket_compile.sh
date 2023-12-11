#!/bin/bash

i=0
while [ -f /usercode/code$i.* ]; do
	# compile and run C file
	if [ -f /usercode/code$i.c ]; then
		(nice -n 15 gcc -o /usercode/code$i /usercode/code$i.c 2>> /usercode/errorc.txt -lm && nice -n 15 ./usercode/code$i >> head -c 1M /usercode/outputc.txt) &
	fi

	# compile and run Python file
	if [ -f /usercode/code$i.py ]; then
		(nice -n 15 python3 /usercode/code$i.py 2>> /usercode/errorc.txt >> head -c 1M /usercode/outputc.txt) &
	fi
	i=$((i + 1))
done

#any update in main must be recompilled
#gcc -o gladCodeServerMain gladCodeServerMain.c -lm -lpthread

nice -n 15 ./usercode/gladCodeServerMain $i >> /usercode/outputs.txt
