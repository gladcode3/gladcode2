#!/bin/bash

set -e

cont=$(docker run -it -d -v /home/gladcode/temp/$1:/usercode --cpu-period=100000 --cpu-quota=50000 virtual_machine /usercode/socket_compile.sh 2>> /home/gladcode/temp/$1/error.txt)

sleep 30 && docker kill $cont &> /dev/null && echo -e "Tempo limite excedido (15s)\n" >> /home/gladcode/temp/$1/error.txt &

docker wait "$cont" &> /dev/null

docker rm -f $cont &> /dev/null
