#!/bin/bash

yum update -y
yum install -y docker

systemctl enable docker

groupadd docker
service docker restart
usermod -aG docker gladcode

docker build -t 'virtual_machine' - < Dockerfile
