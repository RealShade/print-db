#!/bin/bash

#rm -f /var/lib/mysql/ib_logfile0 \
#bindfs --force-user=mysql --force-group=mysql --create-for-user=1000 --create-for-group=1000 --chown-ignore --chgrp-ignore -o nonempty /mnt/external/mysql /var/lib/mysql \

bindfs --force-user=mysql --force-group=mysql --create-for-user=1000 --create-for-group=1000 --chown-ignore --chgrp-ignore -o nonempty /mnt/external/mysql /var/lib/mysql \
&& bindfs --force-user=www-data --force-group=www-data --create-for-user=1000 --create-for-group=1000 --chown-ignore --chgrp-ignore -o nonempty /mnt/external/vhosts /var/www/vhosts \
&& bindfs --force-user=www-data --force-group=www-data --create-for-user=1000 --create-for-group=1000 --chown-ignore --chgrp-ignore -o nonempty /mnt/external/log/nginx /var/log/nginx \
&& rsyslogd \
&& sleep 1 \
&& cp /mnt/external/crontab /etc/crontab \
&& chown root:root /etc/crontab && chmod 644 /etc/crontab \
&& rm -f /var/log/nginx/*.log \
&& service php8.3-fpm start \
&& service cron start \
&& service nginx start \
&& service mariadb start \
&& echo "All started!" \
&& sleep infinity


#sleep infinity

exit $?
