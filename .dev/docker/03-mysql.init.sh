#!/bin/bash

################# set variables
if [ ! -f .env ]; then
    echo "Environment file not found!"
    exit
fi
source .env

################# import DB

if [ ! -d ./config/container/mysql/mysql/$DB_NAME ]; then
  echo "Import DB"
  export $(cut -d= -f1 .env)
  envsubst < ./config/sql/mysql.init.sql > ./config/sql/mysql.init.parsed.sql

  docker exec -i $CONTAINER mysql <./config/sql/mysql.init.parsed.sql
  docker exec -i $CONTAINER mysql $DB_NAME <./config/sql/db.init.sql
  rm ./config/sql/mysql.init.parsed.sql

  echo "Migrating database"
  docker exec -i $CONTAINER bash -c "cd /var/www/vhosts/$PROJECT_FOLDER && php artisan migrate"
else
  echo "DB exists"
fi
