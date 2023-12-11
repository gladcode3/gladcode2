#!/bin/bash

gcc -o /usercode/file /usercode/file.c 2> /usercode/error.txt -lm && ./usercode/file < /usercode/input.txt | head -c 1M > /usercode/output.txt

maxsize=1048576
actualsize=$(wc -c <"/usercode/output.txt")
if [ $actualsize -ge $maxsize ]; then
	echo -e "\nLimite de tamanho do arquivo de saída excedido (1MB)\n" >> /usercode/output.txt
fi

