FROM ubuntu:latest
LABEL authors="Peter Nearing"
# This docker file is rewritten by me, not originally created by me, I got it from an internet
# tutorial.
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
    mailutils \
    nagios-plugin* \
    nrpe-ng

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


WORKDIR /root

# Configure nasios for conf.d directory, so we can quickly add hosts.
# The directory /nagios_conf/ is added as a config directory to the nagios.cfg
RUN mkdir /nagios_conf

COPY etc/nagios/nagios.cfg /usr/local/nagios/etc/nagios.cfg
COPY etc/nagios/commands.cfg /usr/local/nagios/etc/objects/commands.cfg
COPY etc/nagios/contacts.cfg /usr/local/nagios/etc/object/contacts.cfg
COPY etc/nagios/templates.cfg /usr/local/nagios/etc/object/templates.cfg
COPY etc/nagios/timeperiods.cfg /usr/local/nagios/etc/object/timeperiods.cfg

# Configure msmtp
COPY etc/msmtp/msmtprc /etc/msmtprc
RUN echo "user $EMAIL_USERNAME" >> /etc/msmtprc # Set the var for building
RUN echo "password $EMAIL_PASSWORD" >> /etc/msmtprc # Set the var for building

# Using Coolify the following is un-needed. Use the coolify env to set
# the variables NAGIOSADMIN_USER and NAGIOSADMIN_PASSWORD
# Copy the Nagios basic auth credentials set in the env file;
#COPY .env /usr/local/nagios/etc/

# Add Nagios and Apache Startup script
ADD start.sh /
RUN chmod +x /start.sh

EXPOSE 80

CMD [ "/start.sh" ]
