#!/bin/sh

echo "Applying permissions to directory $1"
chmod -R 755 $1
cwd=`pwd`
cd $1
echo "Running git Update"
git config core.filemode false
git pull
rm -r install/
cd $cwd
echo "Reapplying permissions to directory $1"
chmod -R 555 $1
find $1 -type f -name '*.php' -exec chmod 511 {} \;
chmod -R 711 $1/cache
chmod -R 711 $1/templates/content
chmod -R 511 $1/config
