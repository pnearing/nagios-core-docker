#!/bin/bash

# Check if any of the nagios / nagiosgraph var and etc are empty, and copy example data:
if [ -z "$(ls -A /nagios_etc)" ]; then
    echo "Started with empty ETC, copying example data in-place"
    cp -Rp /orig/etc/* /nagios_etc/
    chown ${NAGIOS_USER}:${NAGIOS_GROUP} /nagios_etc
    echo "\$USER2\$=/nagios_handlers" >> /nagios_etc/resource.cfg
    echo "\$USER3\$=/nagios_plugins" >> /nagios_etc/rsource.cfg
fi

if [ -z "$(ls -A /nagios_var)" ]; then
    echo "Started with empty VAR, copying example data in-place"
    cp -Rp /orig/var/* /nagios_var/
    chown ${NAGIOS_USER}:${NAGIOS_GROUP} /nagios_var
fi

if [ -z "$(ls -A /nagiosgraph_etc)" ]; then
    echo "Started with empty /nagiosgraph_etc, copying example data in-place"
    cp -Rp /orig/graph-etc/* /nagiosgraph_etc/
    chown ${NAGIOS_USER}:${NAGIOS_GROUP} /nagiosgraph_etc
fi

if [ -z "$(ls -A /nagiosgraph_var)" ]; then
    echo "Started with empty /nagiosgraph_var, copying example data in-place"
    cp -Rp /orig/graph-var/* /nagiosgraph_var/
    chown ${NAGIOS_USER}:${NAGIOS_GROUP} /nagiosgraph_var
fi

# Update configuration files with the variable values:
htpasswd -b -c /nagios_etc/htpasswd.users "${NAGIOSADMIN_USER}" "${NAGIOSADMIN_PASSWORD}"
sed -i "s/nagiosadmin/${NAGIOSADMIN_USER}/g" /nagios_etc/cgi.cfg

# Redirect root URL (/) to /nagios
echo 'RedirectMatch ^/$ /nagios' >> /etc/apache2/apache2.conf

# Start Nagios
/etc/init.d/nagios start

#Start Apache
a2dissite 000-default default-ssl
rm -rf /run/apache2/apache2.pid
. /etc/apache2/envvars
. /etc/default/apache-htcacheclean
/usr/sbin/apache2 -D FOREGROUND
