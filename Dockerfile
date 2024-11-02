FROM ubuntu:latest
LABEL authors="Peter Nearing"

# This docker file is rewritten by me, not originally created by me; I got it from an internet
# tutorial, I also didn't write start.sh.  All configurations, however were written by me.
# And I'm also using https://github.com/JasonRivers/Docker-Nagios/ to mangle this together.

#ENV NAGIOS_FQDN=nagios.example.com
#ENV NAGIOSADMIN_USER=nagiosadmin
#ENV NAGIOSADMIN_PASS=nagios
#ENV EMAIL_HOST=smtp.example.com
#ENV EMAIL_FROM=monitoring@example.com
#ENV EMAIL_USER=email_username
#ENV EMAIL_PASS=secret_email_password

ENV NAGIOS_HOME=/opt/nagios
ENV NAGIOS_USER=nagios
ENV NAGIOS_GROUP=nagios
ENV NAGIOS_CMD_USER=nagios
ENV NAGIOS_CMD_GROUP=nagios
ENV NAGIOS_TIMEZONE=America/Toronto
ENV TZ=America/Toronto
#ENV DEBIAN_FRONTEND=noninteractive
ENV NG_NAGIOS_CONFIG_FILE=${NAGIOS_HOME}/etc/nagios.cfg
ENV NG_CGI_DIR=${NAGIOS_HOME}/sbin
ENV NG_WWW_DIR=${NAGIOS_HOME}/share/nagiosgraph
ENV NG_CGI_URL=/cgi-bin
ENV NAGIOS_CORE_REPO=https://github.com/NagiosEnterprises/nagioscore.git
ENV NAGIOS_BRANCH=nagios-4.5.7
ENV NAGIOS_PLUGINS_REPO=https://github.com/nagios-plugins/nagios-plugins.git
ENV NAGIOS_PLUGINS_BRANCH=release-2.4.11
ENV NRPE_REPO=https://github.com/NagiosEnterprises/nrpe.git
ENV NRPE_BRANCH=nrpe-4.1.0
ENV NCPA_URL=https://raw.githubusercontent.com/NagiosEnterprises/ncpa
ENV NCPA_BRANCH=v3.1.1
ENV NCSA_REPO=https://github.com/NagiosEnterprises/nsca.git
ENV NSCA_BRANCH=nsca-2.10.2
ENV NAGIOSGRAPH_REPO=https://git.code.sf.net/p/nagiosgraph/git
ENV NAGIOSTV_VERSION=0.9.2

RUN apt-get update -y
RUN apt-get upgrade -y
#RUN echo 'debconf debconf/frontend select Noninteractive' | debconf-set-selections
#RUN DEBIAN_FRONTEND=noninteractive
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y \
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
RUN ( egrep -i "^${NAGIOS_GROUP}"    /etc/group || groupadd $NAGIOS_GROUP    )
RUN ( egrep -i "^${NAGIOS_CMDGROUP}" /etc/group || groupadd $NAGIOS_CMDGROUP )

# Create the users:
RUN ( id -u $NAGIOS_USER    || useradd --system -d $NAGIOS_HOME -g $NAGIOS_GROUP    $NAGIOS_USER    )
RUN ( id -u $NAGIOS_CMDUSER || useradd --system -d $NAGIOS_HOME -g $NAGIOS_CMDGROUP $NAGIOS_CMDUSER )

# Work in /tmp:
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
    --with-openssl=yes \
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
RUN wget -O ${NAGIOS_HOME}/libexec/check_ncpa.py ${NCPA_URL}/${NCPA_BRANCH}/client/check_ncpa.py  && \
    chmod +x ${NAGIOS_HOME}/libexec/check_ncpa.py

# Build and install ncsa plugin:
RUN git clone ${NCSA_REPO}
WORKDIR /tmp/nsca
RUN git checkout ${NSCA_BRANCH}
RUN ./configure \
    --prefix=${NAGIOS_HOME} \
    --with-nsca-user=${NAGIOS_USER} \
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
RUN git clone ${NAGIOSGRAPH_REPO} nagiosgraph
WORKDIR /tmp/nagiosgraph
RUN ./install.pl --install \
        --prefix /opt/nagiosgraph \
        --nagios-user ${NAGIOS_USER} \
        --www-user www-data \
        --nagios-perfdata-file ${NAGIOS_HOME}/var/perfdata.log \
        --nagios-cgi-url /cgi-bin
RUN cp share/nagiosgraph.ssi ${NAGIOS_HOME}/share/ssi/common-header.ssi
WORKDIR /tmp
RUN rm -rf nagiosgraph

# Install nagiostv:
RUN wget https://github.com/chriscareycode/nagiostv-react/releases/download/v${NAGIOSTV_VERSION}/nagiostv-${NAGIOSTV_VERSION}.tar.gz
RUN tar xf nagiostv-${NAGIOSTV_VERSION}.tar.gz -C /opt/nagios/share/
RUN rm /tmp/nagiostv-${NAGIOSTV_VERSION}.tar.gz

# Configure nagios. This will create the directories '/nagios_etc/', '/nagios_var/',
#   '/nagios_plugins/', '/nagios_handlers/', '/nagiosgraph_etc/', '/nagiosgraph_var/,
#   which are added as mount points for nagios / nagiosgraph config, var, custom plugins,
#   and event handlers.  The /nagios_plugins, and /nagios_handlers are added to resources.cfg
#   as $USER2$ and $USER3$, respectivly.
#
#  The additional logos were downloaded from:
#  https://exchange.nagios.org/directory/Graphics-and-Logos/Images-and-Logos/f_logos/details

# Copy f_logos to logo directory:
COPY logos/f_logos ${NAGIOS_HOME}/share/images/logos/
RUN chown -R ${NAGIOS_USER}:${NAGIOS_GROUP} ${NAGIOS_HOME}/share/images/logos

# Set some default config options:
RUN mkdir ${NAGIOS_HOME}/etc/conf.d
RUN chown ${NAGIOS_USER}:${NAGIOS_GROUP} ${NAGIOS_HOME}/etc/conf.d
RUN echo "\$USER2\$=/nagios_plugins" >> ${NAGIOS_HOME}/etc/resource.cfg
RUN echo "\$USER3\$=/nagios_handlers" >> ${NAGIOS_HOME}/etc/rsource.cfg
RUN echo "default_statusmap_layout=5" >> ${NAGIOS_HOME}/etc/cgi.cfg
RUN echo "use_timezone=${NAGIOS_TIMEZONE}" >> ${NAGIOS_HOME}/etc/nagios.cfg
RUN echo "cfg_dir=${NAGIOS_HOME}/etc/conf.d"

# Copy example etc and var incase the user starts with and empty etc or var
RUN mkdir -p /orig/etc
RUN mkdir -p /orig/var
RUN mkdir -p /orig/graph-etc
RUN mkdir -p /orig/graph-var
RUN cp -rp ${NAGIOS_HOME}/etc/* /orig/etc/
RUN cp -rp ${NAGIOS_HOME}/var/* /orig/var/
RUN cp -rp /opt/nagiosgraph/etc/* /orig/graph-etc/
RUN cp -rp /opt/nagiosgraph/var/* /orig/graph-var/

# Make volume mount points for nagios and nagiosgraph
RUN mkdir /nagios_etc
RUN mkdir /nagios_var
RUN mkdir /nagios_handlers
RUN mkdir /nagios_plugins
RUN mkdir /nagiosgraph_etc
RUN mkdir /nagiosgraph_var

# Remove and link nagios, and nagiosgraph etc and var to their mount points:
RUN rm -rf ${NAGIOS_HOME}/etc && ln -s /nagios_etc ${NAGIOS_HOME}/etc
RUN rm -rf ${NAGIOS_HOME}/var && ln -s /nagios_var ${NAGIOS_HOME}/var
RUN rm -rf /opt/nagiosgraph/etc && ln -s /nagiosgraph_etc /opt/nagiosgraph/etc
RUN rm -rf /opt/nagiosgraph/var && ln -s /nagiosgraph_var /opt/nagiosgraph/var

# Configure msmtp.  The environment variables EMAIL_HOST, EMAIL_FROM, EMAIL_USER and EMAIL_PASS,
#  must be set as build environment variables.  This by default sets msmtp to use TLS, on port
#  587,  If your setup requires different settings, this can be acheived by editing the file:
#  etc/msmtp/msmtprc directly.
COPY etc/msmtp/msmtprc /etc/
RUN echo "host $EMAIL_HOST" >> /etc/msmtprc
RUN echo "from $EMAIL_FROM" >> /etc/msmtprc
RUN echo "user $EMAIL_USER" >> /etc/msmtprc
RUN echo "password $EMAIL_PASS" >> /etc/msmtprc

# Set ServerName and timezone for Apache:
RUN echo "ServerName ${NAGIOS_FQDN}" > /etc/apache2/conf-available/servername.conf
RUN echo "PassEnv TZ" > /etc/apache2/conf-available/timezone.conf
RUN ln -s /etc/apache2/conf-available/servername.conf /etc/apache2/conf-enabled/servername.conf
RUN ln -s /etc/apache2/conf-available/timezone.conf /etc/apache2/conf-enabled/timezone.conf

# Enable apache modules:
RUN a2enmod session
RUN a2enmod session_cookie
RUN a2enmod session_crypto
RUN a2enmod auth_form
RUN a2enmod request

# Using Coolify the following is un-needed. Use the coolify env variables to set
# the variables NAGIOSADMIN_USER and NAGIOSADMIN_PASSWORD as build variables.
# If not using Coolify uncomment this line and add a .env file to the project
# root containing the afore-mentioned credential variables; Be sure to .gitignore the
# .env file!
#COPY .env ${NAGIOS_HOME}/etc/

# Add Nagios and Apache Startup script:
ADD start.sh /
RUN chmod +x /start.sh

# Expose port 80
EXPOSE 80

# Note volumes:
VOLUME /nagios_etc
VOLUME /nagios_var
VOLUME /nagios_handlers
VOLUME /nagios_plugins
VOLUME /nagiosgraph_etc
VOLUME /nagiosgraph_var

# Set the entry point:
CMD [ "/start.sh" ]
