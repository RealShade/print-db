#!/bin/bash

################## set variables
echo "Initialize"

if [ ! -f .env ]; then
    echo "Environment file not found!"
    exit
fi
source .env
#DIRNAME=$(dirname $(readlink -e "$0"))
DIRNAME=$(pwd)
ENVLIST=`cat .env && echo DIRNAME=$DIRNAME`
TEMP_CONTAINER="${CONTAINER}_temp"

################## build container
echo "Build container"

echo "Stopping..."
docker stop $CONTAINER
echo "Removing..."
docker rm $CONTAINER

echo "Building..."
docker build -t $CONTAINER . || exit

################## check and copy mysql and php folders
docker create --name $TEMP_CONTAINER $CONTAINER || exit

echo "Check DB..."
if [ ! -d './config/container/mysql/mysql' ]; then
    echo "Copying mysql DB folder"
    docker cp $TEMP_CONTAINER:/var/lib/mysql/ ./config/container/mysql/mysql || exit
else
    echo "Mysql DB folder already exists"
fi

echo "Check PHP configs..."
if [ ! -d "./config/container/php/8.3/fpm" ]; then
    mkdir -p ./config/container/php/8.3/fpm
    if [ $? -ne 0 ]; then
        echo "Error creating ./config/container/php/8.3/fpm"
        exit 1
    fi
    mkdir -p ./config/container/php/8.3/mods-available
    if [ $? -ne 0 ]; then
        echo "Error creating ./config/container/php/8.3/mods-available"
        exit 1
    fi
fi

if [ -z "$(ls -A ./config/container/php/8.3/fpm)" ]; then
    echo "Copying php8.3-fpm configuration folder"
    docker cp $TEMP_CONTAINER:/etc/php/8.3/fpm/pool.d ./config/container/php/8.3/fpm/
    docker cp $TEMP_CONTAINER:/etc/php/8.3/fpm/php-fpm.conf ./config/container/php/8.3/fpm/
    docker cp $TEMP_CONTAINER:/etc/php/8.3/fpm/php.ini ./config/container/php/8.3/fpm/
    docker cp $TEMP_CONTAINER:/etc/php/8.3/mods-available/xdebug.ini ./config/container/php/8.3/mods-available/
fi

docker rm $TEMP_CONTAINER || exit

################## start container ang get IP

#docker compose build --no-cache || exit
bash ./up.sh -d

CONTAINERIP=`docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' ${CONTAINER}`
exit
bash ./down.sh

################## done

echo "+----------------------------------------------------------------------------------------------------+"
echo "|                                                                                                    |"
echo "| Please import CA into your browser                                                                 |"
echo "| 1. Go to Settings->Private and security->Security->Manage certificates                             |" 
echo "| 2. On tab 'Authorities' press the button 'Import' and select the file ./config/ssl/myCA.pem        |"
echo "| 3. Mark the option 'Thrust the certificate for identifying websites'                               |"
echo "| 4. All done                                                                                        |"
echo "|                                                                                                    |"
echo "| If you need to working with site from sysytem, import sertificate to your system                   |"
echo "|   sudo cp ./config/ssl/realshademeCA.pem /usr/local/share/ca-certificates/realshademeCA.crt                              |"
echo "|   sudo update-ca-certificates                                                                      |"
echo "|                                                                                                    |"
echo "+----------------------------------------------------------------------------------------------------+"

echo "******************************************************************************************************"
echo " Please add to /etc/hosts next lines:"
echo ""
echo "$CONTAINERIP dev-print-db.realshade.me"
echo ""
echo "******************************************************************************************************"
