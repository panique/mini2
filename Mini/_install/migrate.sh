#!/usr/bin/env bash

# vagrant up によってprovisioningを行った後、データベースを更新したい時のためのシェルスクリプト

# Use single quotes instead of double quotes to make it work with special-character passwords
PASSWORD='12345678'
PROJECTFOLDER='myproject'


sudo mysql -h "localhost" -u "root" "-p${PASSWORD}" < "/var/www/html/${PROJECTFOLDER}/Mini/_install/05-create-table-onaona.sql"


