FROM ubuntu:latest
LABEL authors="Peter Nearing"

# This docker file is rewritten by me, not originally created by me; I got it from an internet
# tutorial, I also didn't write start.sh.  All configurations, however were written by me.

RUN apt update -y
RUN apt upgrade -y
RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
RUN DEBIAN_FRONTEND=noninteractive
RUN apt install -y \
	autoconf \
	gcc \
	libc6 \
	make \
	wget \
	unzip \
	apache2 \
	apache2-utils \
	php \
	libapache2-mod-php \
	libgd-dev \
	libssl-dev \
	libmcrypt-dev \
	bc \
	gawk \
	dc \
	build-essential \
	snmp \
	libnet-snmp-perl \
	gettext \
	fping \
    iputils-ping \
	qstat \
	dnsutils \
	smbclient \
    msmtp \
    msmtp-mta \
    mailutils

# Building Nagios Core
COPY nagios-4.5.7 /nagios-4.5.7
WORKDIR /nagios-4.5.7
RUN ./configure --with-httpd-conf=/etc/apache2/sites-enabled && \
    make all && \
    make install-groups-users && \
    usermod -aG nagios www-data && \
    make install && \
    make install-init && \
    make install-daemoninit && \
    make install-commandmode && \
    make install-config && \
    make install-webconf && \
    a2enmod rewrite cgi

# Building Nagios Plugins
COPY nagios-plugins-2.4.11 /nagios-plugins-2.4.11
WORKDIR /nagios-plugins-2.4.11
RUN ./configure --with-nagios-user=nagios --with-nagios-group=nagios && \
    make && \
    make install

# Build and Install NRPE Plugins
COPY nrpe-4.1.0 /nrpe-4.1.0
WORKDIR /nrpe-4.1.0
RUN ./configure && \
    make all && \
    make install-plugin

# Install additional plugins, and images using apt:
RUN apt install -y nagios-plugins* nagios-snmp-plugins nagios-images

# Configure nagios, if the environment variable DISABLE_LOCALHOST is set to either
#  1 or true, then the config for the default localhost 'host' is removed from nagios.cfg,
#  otherwise 'localhost' is renamed to 'docker_container' to better describe what
#  is actually being monitored. If localhost is disabled, then nagios will fail to run properly
#  until you define a host. If localhost is enabled, then the SSH service will fail, since this
#  container doesn't include a ssh daemon. This will also create the directories '/config/',
#  which is added to the nagios.cfg to be a config directory to use as a volume mount point,
#  '/event_handlers/' to use as a volume mount point for custom event handlers, as well as
#  '/plugins/ to use as a volume mount point for custom plugins.  It also adds the apt plugin
#  config directory to the nagios.cfg, to add the commands for the apt installed plugins.
#  This will copy a customized commands.cfg file, removing duplicate commands that are also
#  defined in /etc/nagios-plugins/config.
#
#  In addition, this also adds the following custom variable/macros to the resources.cfg:
#       $USER2$=/usr/lib/nagios/plugins;    The location apt installs nagios plugins.
#       $USER3$=/usr/lib/nagios/plugins-rabbitmq;  The location apt installs the rabbitmq plugins.
#       $USER4$=/event_handlers;    The location of custom event handlers.
#       $USER5$=/plugins;       The location of custom plugins.

#ENV DISABLE_LOCALHOST=true

WORKDIR /root
COPY etc/nagios/commands.cfg /usr/local/nagios/etc/objects/commands.cfg
COPY configure_nagios.sh .
RUN chmod +x configure_nagios.sh
RUN ./configure_nagios.sh

# Configure msmtp.  The environment variables EMAIL_HOST, EMAIL_FROM, EMAIL_USER and EMAIL_PASS,
#  must be set as build environment variables.  This by default sets msmtp to use TLS, on port
#  587,  If your setup requires different settings, this can be acheived by editing the file:
#  etc/msmtp/msmtprc directly.
#       EMAIL_HOST=<your smtp relay hostname / address>
#       EMAIL_FROM=<the from address for all outgoing email.>
#       EMAIL_USER=<your smtp username>
#       EMAIL_PASS=<your smtp password>
COPY etc/msmtp/msmtprc /etc/msmtprc
COPY configure_msmtp.sh .
RUN chmod +x configure_msmtp.sh
RUN ./configure_msmtp.sh

# Using Coolify the following is un-needed. Use the coolify env variables to set
# the variables NAGIOSADMIN_USER and NAGIOSADMIN_PASSWORD as build variables.
# If not using Coolify uncomment this line and add a .env file to the project
# root containing the afore-mentioned credential variables.
#COPY .env /usr/local/nagios/etc/

# Add Nagios and Apache Startup script:
ADD start.sh /
RUN chmod +x /start.sh

# Expose port 80
EXPOSE 80

# Set the entry point:
CMD [ "/start.sh" ]
