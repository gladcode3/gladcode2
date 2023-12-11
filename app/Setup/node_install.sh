#!/bin/bash

yum install -y gcc-c++ make
curl -sL https://rpm.nodesource.com/setup_10.x | sudo -E bash -
sudo yum install nodejs
npm install express --save
npm install express-session --save
npm install express-mysql-session --save
npm install mysql --save
npm install socket.io --save
npm install body-parser --save
npm install cors --save
npm install request --save
npm install pm2 -g