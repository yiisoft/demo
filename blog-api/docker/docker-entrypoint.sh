#!/bin/sh
set -e

ls -la
# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- php-fpm "$@"
fi
set -e

if [ "$1" = 'php-fpm' ] || [ "$1" = 'php' ] || [ "$1" = 'yii' ]; then
	setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX runtime public
	setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX runtime public
fi

exec "$@"
