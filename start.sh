#!/bin/bash

# The following is un-needed if using coolify, use the coolify env vars instead.
# Load the credentials variables
#source /usr/local/nagios/etc/.env

# Override the environment variables if passed as arguments during docker run
if [ -n "$NAGIOSADMIN_USER_OVERRIDE" ]; then
    export NAGIOSADMIN_USER="$NAGIOSADMIN_USER_OVERRIDE"
fi

if [ -n "$NAGIOSADMIN_PASSWORD_OVERRIDE" ]; then
    export NAGIOSADMIN_PASSWORD="$NAGIOSADMIN_PASSWORD_OVERRIDE"
fi

# Update configuration files with the variable values, considering overrides
htpasswd -b -c /usr/local/nagios/etc/htpasswd.users "${NAGIOSADMIN_USER_OVERRIDE:-$NAGIOSADMIN_USER}" "${NAGIOSADMIN_PASSWORD_OVERRIDE:-$NAGIOSADMIN_PASSWORD}"
sed -i "s/nagiosadmin/${NAGIOSADMIN_USER_OVERRIDE:-$NAGIOSADMIN_USER}/g" /usr/local/nagios/etc/cgi.cfg

# Redirect root URL (/) to /nagios
echo 'RedirectMatch ^/$ /nagios' >> /etc/apache2/apache2.conf

# Start Nagios
/etc/init.d/nagios start

# Start postfix
systemctl start postfix

#Start Apache
a2dissite 000-default default-ssl
rm -rf /run/apache2/apache2.pid
. /etc/apache2/envvars
. /etc/default/apache-htcacheclean
/usr/sbin/apache2 -DFOREGROUND
