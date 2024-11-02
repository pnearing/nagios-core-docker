#!/bin/bash


# Update configuration files with the variable values, considering overrides
# shellcheck disable=SC2082
htpasswd -b -c /opt/nagios/etc/htpasswd.users "${$NAGIOSADMIN_USER}" "${$NAGIOSADMIN_PASSWORD}"
sed -i "s/nagiosadmin/${$NAGIOSADMIN_USER}/g" /usr/local/nagios/etc/cgi.cfg

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
