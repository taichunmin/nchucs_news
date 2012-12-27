@echo off
echo Start daily work...
echo getnews_udn.php
php -C -f getnews_udn.php
echo.
echo ckipsvr.php
php -C -f ckipsvr.php
echo.
echo ontology.php
php -C -f ontology.php
echo.
echo similarity.php
php -C -f similarity.php
echo.
echo Finish!
@echo on
