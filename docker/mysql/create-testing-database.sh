#!/usr/bin/env bash
# Sail-compatible testing DB setup, fixed for MySQL 8.4+ (ERROR 1410 on GRANT when user
# does not exist). Uses -e with double-quoted SQL so $MYSQL_USER / $MYSQL_PASSWORD expand.

set -e

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS testing;
EOSQL

if [ -n "$MYSQL_USER" ] && [ -n "$MYSQL_PASSWORD" ]; then
  mysql --user=root --password="$MYSQL_ROOT_PASSWORD" -e "
    CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'%' IDENTIFIED BY '${MYSQL_PASSWORD}';
    GRANT ALL PRIVILEGES ON \`testing%\`.* TO '${MYSQL_USER}'@'%';
    FLUSH PRIVILEGES;
  "
fi
