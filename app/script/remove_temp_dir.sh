#!/bin/bash

now=$(date +%s)

for dir in /home/gladcode/temp/*/
do
  if [[ "$dir" =~ ([a-f0-9]{32})\/ ]]; then
    file=$(date +%s -r $dir)
    diff=$((now - file))

    if [ $diff -ge "86400" ]; then
      rm -rf $dir
    fi
  fi
done
