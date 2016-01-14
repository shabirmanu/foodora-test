#!/bin/bash

RUNDATE=`2015-12-20 23:00:00`
/usr/local/bin/php /var/www/html/Sandbox/foodora-test/backup.php $RUNDATE

RUNDATE=`2015-12-28 01:00:00`
/usr/local/bin/php /var/www/html/Sandbox/foodora-test/restore.php $RUNDATE