#!/bin/sh
export XDEBUG_CONFIG="idekey=PHPSTORM remote_host=localhost remote_port=9013"
export PHP_IDE_CONFIG="serverName=fsbo"
/var/www/html/citygram/yii $1 $2 $3 $4