#!/bin/bash

rm -rf /var/lib/docker/

service docker restart
usermod -aG docker gladcode

docker build -t 'virtual_machine' - < Dockerfile

