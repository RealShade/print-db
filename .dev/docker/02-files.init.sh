#!/bin/bash

################# set variables
echo "Initialize"

if [ ! -f .env ]; then
    echo "Environment file not found!"
    exit
fi
source .env
#DIRNAME=$(dirname $(readlink -e "$0"))
DIRNAME=$(pwd)
ENVLIST=`cat .env && echo DIRNAME=$DIRNAME`

################# create symlinks & copying files

