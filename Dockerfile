FROM ubuntu:latest
LABEL authors="Peter Nearing"

# This docker file is rewritten by me, not originally created by me; I got it from an internet
# tutorial, I also didn't write start.sh.  All configurations, however were written by me.
# And I'm also using https://github.com/JasonRivers/Docker-Nagios/ to mangle this together.

ENV NAGIOS_HOME            /opt/nagios
ENV NAGIOS_USER            nagios
ENV NAGIOS_GROUP           nagios
ENV NAGIOS_CMD_USER         nagios
ENV NAGIOS_CMD_GROUP        nagios
#ENV NAGIOS_FQDN            nagios.example.com
#ENV NAGIOSADMIN_USER       nagiosadmin
#ENV NAGIOSADMIN_PASS       nagios
ENV APACHE_RUN_USER        www-data
ENV APACHE_RUN_GROUP       www-data
ENV NAGIOS_TIMEZONE        America/Toronto
ENV DEBIAN_FRONTEND        noninteractive
ENV NG_NAGIOS_CONFIG_FILE  ${NAGIOS_HOME}/etc/nagios.cfg
ENV NG_CGI_DIR             ${NAGIOS_HOME}/sbin
ENV NG_WWW_DIR             ${NAGIOS_HOME}/share/nagiosgraph
ENV NG_CGI_URL             /cgi-bin
ENV NAGIOS_CORE_REPO       https://github.com/NagiosEnterprises/nagioscore.git
ENV NAGIOS_BRANCH          nagios-4.5.7
ENV NAGIOS_PLUGINS_REPO    https://github.com/nagios-plugins/nagios-plugins.git
ENV NAGIOS_PLUGINS_BRANCH  release-2.4.11
ENV NRPE_REPO              https://github.com/NagiosEnterprises/nrpe.git
ENV NRPE_BRANCH            nrpe-4.1.0

ENV NCPA_BRANCH            v3.1.1

ENV NSCA_BRANCH            nsca-2.10.2

ENV NAGIOSTV_VERSION       0.9.2

RUN apt update -y
RUN apt upgrade -y
RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
RUN DEBIAN_FRONTEND=noninteractive
RUN apt install -y \
    apache2 \
    apache2-utils \
    autoconf \
    automake \
    bc \
    bsd-mailx \
    build-essential \
    dnsutils \
    fping \
    gettext \
    git \
    gperf \
    iputils-ping \
    jq \
    libapache2-mod-php \
    libcache-memcached-perl \
    libcgi-pm-perl \
    libcrypt-des-perl \
    libcrypt-rijndael-perl \
    libcrypt-x509-perl \
    libdbd-mysql-perl \
    libdbd-pg-perl \
    libdbi-dev \
    libdbi-perl \
    libdigest-hmac-perl \
    libfreeradius-dev \
    libgdchart-gd2-xpm-dev \
    libgd-gd2-perl \
    libjson-perl \
    libldap2-dev \
    libmonitoring-plugin-perl \
    libmysqlclient-dev \
    libnagios-object-perl \
    libnet-snmp-perl \
    libnet-snmp-perl \
    libnet-tftp-perl \
    libnet-xmpp-perl \
    libpq-dev \
    libradsec-dev \
    libredis-perl \
    librrds-perl \
    libssl-dev \
    libswitch-perl \
    libtext-glob-perl \
    libwww-perl \
    m4 \
    netcat-traditional \
    parallel \
    php-cli \
    php-gd \
    python3-pip \
    python3-nagiosplugin \
    rsync \
    rsyslog \
    runit \
    smbclient \
    snmp \
    snmpd \
    snmp-mibs-downloader \
    unzip \
    python3 \
    gcc \
    libc6 \
    make \
    wget \
    unzip \
    libgd-dev \
    libmcrypt-dev \
    gawk \
    dc \
    msmtp \
    msmtp-mta \
    mailutils

RUN apt-get clean

# Create the groups:
RUN ( egrep -i "^${NAGIOS_GROUP}"    /etc/group || groupadd $NAGIOS_GROUP    )                         && \
    ( egrep -i "^${NAGIOS_CMDGROUP}" /etc/group || groupadd $NAGIOS_CMDGROUP )

# Create the users:
RUN ( id -u $NAGIOS_USER    || useradd --system -d $NAGIOS_HOME -g $NAGIOS_GROUP    $NAGIOS_USER    )  && \
    ( id -u $NAGIOS_CMDUSER || useradd --system -d $NAGIOS_HOME -g $NAGIOS_CMDGROUP $NAGIOS_CMDUSER )

WORKDIR /tmp

# Build and install Nagios Core:
RUN echo "Building nagios core"
RUN git clone "${NAGIOS_CORE_REPO}" -b "$NAGIOS_BRANCH"
WORKDIR /tmp/nagioscore
RUN ./configure \
    --with-httpd-conf=/etc/apache2/sites-enabled \
    --prefix=${NAGIOS_HOME} \
    --exec-prefix=${NAGIOS_HOME} \
    --enable-event-broker \
    --with-command-user=${NAGIOS_CMD_USER} \
    --with-command-group=${NAGIOS_CMD_GROUP} \
    --with-nagios-user=${NAGIOS_USER} \
    --with-nagios-group=${NAGIOS_GROUP}
RUN make all &&\
    make install-groups-users && \
    usermod -aG nagios www-data && \
    make install && \
    make install-init && \
    make install-daemoninit && \
    make install-commandmode && \
    make install-config && \
    make install-webconf && \
    make clean &&\
    a2enmod rewrite cgi
WORKDIR /tmp
RUN rm -rf nagioscore

## Build and install Nagios Plugins:
RUN git clone "${NAGIOS_PLUGINS_REPO}" -b "${NAGIOS_PLUGINS_BRANCH}"
WORKDIR /tmp/nagios-plugins
RUN ./tools/setup
RUN ./configure \
    --prefix="${NAGIOS_HOME}" \
    --with-ipv6 \
    --with-ping-command="/usr/bin/ping -n -U -W %d -c %d %s" \
    --with-ping6-command="/usr/bin/ping -6 -n -U -W %d -c %d %s" \
    --with-nagios-user="${NAGIOS_USER}" \
    --with-nagios-group="${NAGIOS_GROUP}" \
    --with-openssl=auto \
    --with-libmount
RUN make && make install
RUN mkdir -p /usr/lib/nagios/plugins &&\
  ln -sf "${NAGIOS_HOME}/libexec/utils.pm" /usr/lib/nagios/plugins &&\
  chown root:root "${NAGIOS_HOME}/libexec/check_icmp" &&\
  chmod u+s "${NAGIOS_HOME}/libexec/check_icmp"
RUN make clean
WORKDIR /tmp
RUN rm -rf nagios-plugins

# Build and Install NRPE Plugins
RUN git clone $NRPE_REPO -b $NRPE_BRANCH
WORKDIR /tmp/nrpe
RUN ./configure \
    --with-ssl=/usr/bin/openssl \
    --with-ssl-lib=/usr/lib/x86_64-linux-gnu \
    --with-nagios-user="${NAGIOS_USER}" \
    --with-nagios-group="${NAGIOS_GROUP}"
RUN make check_nrpe
RUN cp src/check_nrpe "${NAGIOS_HOME}/libexec/"
RUN make clean
WORKDIR /tmp
RUN rm -rf nrpe

# Fetch and install ncpa plugin:
RUN wget -O ${NAGIOS_HOME}/libexec/check_ncpa.py https://raw.githubusercontent.com/NagiosEnterprises/ncpa/${NCPA_BRANCH}/client/check_ncpa.py  && \
    chmod +x ${NAGIOS_HOME}/libexec/check_ncpa.py

# Build and install ncsa plugin:
RUN git clone https://github.com/NagiosEnterprises/nsca.git
WORKDIR /tmp/nsca
RUN git checkout ${NSCA_BRANCH}
RUN ./configure \
    --prefix=${NAGIOS_HOME}                                \
    --with-nsca-user=${NAGIOS_USER}                        \
    --with-nsca-grp=${NAGIOS_GROUP}
RUN make all
RUN cp src/nsca ${NAGIOS_HOME}/bin/
RUN cp src/send_nsca ${NAGIOS_HOME}/bin/
RUN cp sample-config/nsca.cfg ${NAGIOS_HOME}/etc/
RUN cp sample-config/send_nsca.cfg ${NAGIOS_HOME}/etc/
RUN sed -i 's/^#server_address.*/server_address=0.0.0.0/'  ${NAGIOS_HOME}/etc/nsca.cfg
WORKDIR /tmp
RUN rm -rf nsca

# Install nagiosgraph:
RUN git clone https://git.code.sf.net/p/nagiosgraph/git nagiosgraph
WORKDIR /tmp/nagiosgraph
RUN ./install.pl --install                                      \
        --prefix /opt/nagiosgraph                               \
        --nagios-user ${NAGIOS_USER}                            \
        --www-user www-data                               \
        --nagios-perfdata-file ${NAGIOS_HOME}/var/perfdata.log  \
        --nagios-cgi-url /cgi-bin
RUN cp share/nagiosgraph.ssi ${NAGIOS_HOME}/share/ssi/common-header.ssi
WORKDIR /tmp
RUN rm -rf nagiosgraph




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
#  The additional logos were downloaded from:
#  https://exchange.nagios.org/directory/Graphics-and-Logos/Images-and-Logos/f_logos/details

#ENV DISABLE_LOCALHOST=true

WORKDIR /root
#COPY etc/nagios/commands.cfg ${NAGIOS_HOME}/etc/objects/commands.cfg
COPY logos/f_logos ${NAGIOS_HOME}/share/images/logos/f_logos
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
