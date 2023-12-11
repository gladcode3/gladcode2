#!/bin/bash

set -e

cont=$(docker run -it -d -v /home/gladcode/temp/$1:/usercode virtual_machine /usercode/compilerun.sh 2>> /home/gladcode/temp/$1/error.txt)

sleep 10 && docker kill $cont &> /dev/null && echo -e "Tempo limite excedido (10s)\n" >> /home/gladcode/temp/$1/error.txt &

docker wait "$cont" &> /dev/null

docker rm -f $cont &> /dev/null
