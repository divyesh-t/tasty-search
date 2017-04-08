#!/bin/bash

set -e

dump=${1:-tables.sql}
self=$(dirname $0)
source "$self/../../.env"

function usage_conf_editor {
cat <<-USAGE
	PRE-REQUISITE:
	For using this script please make sure that you have mysql_config_editor.
	On Debian (and derivatives, like Ubuntu): apt-get install libmysqlclient-dev

	Once you've got mysql_config_editor installed, you can set the root password as follows:
	\$> mysql_config_editor set --login-path=client --host=${DB_HOST} --user=root --password

	Or you could create .my.ini file or whatever; just make sure mysql -uroot does not need a password.

	#Docs on the tool
	#https://opensourcedbms.com/dbms/passwordless-authentication-using-mysql_config_editor-with-mysql-5-6/
USAGE
}

which mysql_config_editor > /dev/null || usage_conf_editor

echo "Resetting mysql://$DB_HOST/$DB_DATABASE, user=root, password=read from mysql_config_editor"
echo "New schema: $self/$dump"

mysql -uroot -h ${DB_HOST} < ${self}/${dump}

#uncomment the below command if for some reason you cant run mysql_config_editor
#mysql --user=$DB_USERNAME --password=$DB_PASSWORD --host=$DB_HOST  --port=$DB_PORT < $self/$dump
