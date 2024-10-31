#!/bin/bash

# Check if we should disable the default localhost host entry, if so, remove the
#  cfg_file line from the default nagios.cfg, otherwise, rename the localhost to
#  nagios_container to better show what is being monitored.
if [ "$DISABLE_LOCALHOST" -eq 1 ] || [ "$DISABLE_LOCALHOST" == "true" ]; then
  sed -i 's/cfg_file=\/usr\/local\/nagios\/etc\/objects\/localhost.cfg/#/' /usr/local/nagios/etc/nagios.cfg
else
  sed -i 's/localhost/nagios_container/' /usr/local/nagios/etc/objects/localhost.cfg
fi

# Make directories to hold configs, custom event handlers, and custom plugins:
mkdir /config
mkdir /event_handlers
mkdir /plugins

# Add the config directory to the nagios config:
echo "cfg_dir=/config" >> /usr/local/nagios/etc/nagios.cfg

# Add the apt installed plugins config directory to nagios.cfg:
echo "cfg_dir=/etc/nagios-plugins/config" >> /usr/local/nagios/etc/nagios.cfg

# Add the installed directories to the resources.cfg, useable as
#  $USER2$ for the default apt plugin location, $USER3$ for the rabbitmq plugin location,
#  $USER4$ for custom event handlers location, and $USER5$ for custom plugin location.
echo "\$USER2\$=/usr/lib/nagios/plugins" >> /usr/local/nagios/resource.cfg
echo "\$USER3\$=/usr/lib/nagios/plugins-rabbitmq" >> /usr/local/nagios/resource.cfg
echo "\$USER4\$=/event_handlers"
echo "\$USER5\$=/plugins"

# Set the default satus map to circular (balloon):
echo "default_statusmap_layout=6" >> /usr/local/nagios/etc/cgi.cfg

# Copy the image directories from /usr/share/nagios/htdocs/images/logos/ ->
#   /usr/local/nagios/share/images/logos/
cp -ra /usr/share/nagios/htdocs/images/logos/* /usr/local/nagios/share/images/logos/