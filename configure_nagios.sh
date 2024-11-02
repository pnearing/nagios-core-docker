#!/bin/bash

# Check if we should disable the default localhost host entry, if so, remove the
#  cfg_file line from the default nagios.cfg, otherwise, rename the localhost to
#  nagios_container to better show what is being monitored.
if [ "$DISABLE_LOCALHOST" -eq 1 ] || [ "$DISABLE_LOCALHOST" == "true" ]; then
  echo "Disabling localhost"
  sed -i 's/cfg_file=\/usr\/local\/nagios\/etc\/objects\/localhost.cfg/#/' ${NAGIOS_HOME}/etc/nagios.cfg
else
  sed -i 's/localhost/nagios_container/' "${NAGIOS_HOME}/etc/objects/localhost.cfg"
fi

# Make directories to hold configs, custom event handlers, and custom plugins:
mkdir /config
mkdir /event_handlers
mkdir /plugins

# Add the config directory to the nagios config:
echo "cfg_dir=/config" >> "${NAGIOS_HOME}/etc/nagios.cfg"

# Add the apt installed plugins config directory to nagios.cfg:
#echo "cfg_dir=/etc/nagios-plugins/config" >> ${NAGIOS_HOME}/etc/nagios.cfg

# Add the installed directories to the resources.cfg, usable as
#  $USER2$ for the default apt plugin location, $USER3$ for the rabbitmq plugin location,
#  $USER4$ for custom event handlers location, and $USER5$ for custom plugin location.
#echo "\$USER2\$=/usr/lib/nagios/plugins" >> "${NAGIOS_HOME}/resource.cfg"
#echo "\$USER3\$=/usr/lib/nagios/plugins-rabbitmq" >> "${NAGIOS_HOME}/resource.cfg"
echo "\$USER4\$=/event_handlers"
echo "\$USER5\$=/plugins"

# Set the default satus map to circular (Marked up):
echo "default_statusmap_layout=5" >> "${NAGIOS_HOME}/etc/cgi.cfg"

# Copy the image directories from /usr/share/nagios/htdocs/images/logos/ ->
#   ${NAGIOS_HOME}/share/images/logos/
#cp -r /usr/share/nagios/htdocs/images/logos/* ${NAGIOS_HOME}/share/images/logos/
#chown -R --from root:root nagios:nagios ${NAGIOS_HOME}/share/images/logos