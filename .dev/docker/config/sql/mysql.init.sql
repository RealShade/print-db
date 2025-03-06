CREATE DATABASE `$DB_NAME` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

create user '$DB_USER'@'%' identified by '$DB_PASS';
grant super on *.* to '$DB_USER'@'%';
grant all privileges on *.* to '$DB_USER'@'%';
flush privileges;
