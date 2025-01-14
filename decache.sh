#!/bin/bash

rm -f themes/*/templates_cached/*/*.tcd
rm -f themes/*/templates_cached/*/*.tcp
rm -f themes/*/templates_cached/*/*.cache
rm -f themes/*/templates_cached/*/*.js
rm -f themes/*/templates_cached/*/*.css
rm -f themes/*/templates_cached/*/*.gz
rm -f safe_mode_temp/*.dat
rm -f caches/lang/*/*.lcd
find caches -name "*.gcd" -exec rm -f {} \;
find caches -name "*.htm" -exec rm -f {} \;
if [ -e "data_custom/failover_rewritemap.txt" ]; then
	echo > data_custom/failover_rewritemap.txt
	echo > data_custom/failover_rewritemap__mobile.txt
fi
echo $'\n\ndefine(\'DO_PLANNED_DECACHE\', true);' >> _config.php

if [ -e "sites" ]; then
   find . -name "*.tcd" -exec rm -f {} \;
   find . -name "*.tcp" -exec rm -f {} \;
   find . -name "*.lcd" -exec rm -f {} \;
   find sites -name "*.js" -exec rm -f {} \;
   find sites -name "*.css" -exec rm -f {} \;
fi

if [ -e "../decache.php" ]; then
    # Useful script, outside of web dir, for doing custom decaching
	php ../decache.php
fi
