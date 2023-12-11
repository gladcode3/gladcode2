##!/bin/bash

DATA=`date +%Y-%m-%d-%H-%M`

mysqldump gladcode_ > $DATA.sql
gzip $DATA.sql
rclone copy $DATA.sql.gz gdrive:/1NOSYNC/gladcode_backup/
rm -rf $DATA.sql.gz

