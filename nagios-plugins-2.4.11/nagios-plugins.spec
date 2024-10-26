# Macros
%define isaix %(test "`uname -s`" = "AIX" && echo "1" || echo "0")
%define islinux %(test "`uname -s`" = "Linux" && echo "1" || echo "0")
%define isredhatfamily %(test -f /etc/redhat-release && echo "1" || echo "0")

%if %{isaix}
	%define _prefix /opt/nagios
#	%define _defaultdocdir %{_datadir}/doc
%else
	%define _libexecdir %{_exec_prefix}/lib/nagios/plugins
%endif
%define _sysconfdir /etc/nagios

%define npusr nagios
%define nphome /opt/nagios
%define npgrp nagios

Name: nagios-plugins
Version: 2.4.11
Release: 1
Summary: Host/service/network monitoring program plugins for Nagios

Group: Applications/System
License: GPL
URL: https://www.nagios-plugins.org/
Source0: https://www.nagios-plugins.org/download/%{name}-%{version}.tar.gz
BuildRoot: %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n)

%define npdir %{_builddir}/%{name}-%{version}

%if %{isaix}
Prefix: %{_prefix}
%else
Prefix: %{_prefix}/lib/nagios/plugins
%endif
Packager: Karl DeBisschop <kdebisschop@users.sourceforge.net>
Vendor: Nagios Plugin Development Group
Provides: nagios-plugins

%{!?custom:%global custom 0}
Obsoletes: nagios-plugins-custom nagios-plugins-extras


# Requires
%if %{isaix}
Requires:	fping 
Requires:	gawk
Requires:	net-snmp 
Requires:	net-snmp-perl 
Requires:	net-snmp-utils
Requires:	openldap
Requires:	openssl
Requires:	perl
Requires:	python
Requires:	openssl
BuildRequires:	fping 
BuildRequires:	gawk
BuildRequires:	net-snmp 
BuildRequires:	net-snmp-perl 
BuildRequires:	net-snmp-utils
BuildRequires:	openldap-devel
%endif
%if %{isredhatfamily}
Requires:	bind-utils
Requires:	coreutils
Requires:	fping 
Requires:	gawk
Requires:	grep
Requires:	iputils
Requires:	mysql
Requires:	net-snmp-utils
Requires:	ntp
Requires:	openldap
Requires:	openssl
Requires:	openssh-clients
Requires:	perl
Requires:	postgresql-libs
Requires:	procps
Requires:	python
Requires:	samba-client
Requires:	shadow-utils
Requires:	traceroute
Requires:	/usr/bin/mailq
BuildRequires:	bind-utils
BuildRequires:	coreutils
BuildRequires:	iputils
BuildRequires:	mysql-devel
BuildRequires:	net-snmp-utils
BuildRequires:	net-tools
BuildRequires:	ntp
BuildRequires:	openldap-devel
BuildRequires:	openssh-clients
BuildRequires:	openssl-devel
BuildRequires:	postgresql-devel
BuildRequires:	procps
BuildRequires:	samba-client
BuildRequires:	/usr/bin/mailq
%endif


%description

Nagios is a program that will monitor hosts and services on your
network, and to email or page you when a problem arises or is
resolved. Nagios runs on a unix server as a background or daemon
process, intermittently running checks on various services that you
specify. The actual service checks are performed by separate "plugin"
programs which return the status of the checks to Nagios. This package
contains those plugins.


%prep
%setup -q


%build
%{?isaix: MAKE=gmake} ./configure \
--prefix=%{_prefix} \
--exec-prefix=%{_exec_prefix} \
--libexecdir=%{_libexecdir} \
--sysconfdir=%{_sysconfdir} \
--datadir=%{_datadir} \
--with-cgiurl=/nagios/cgi-bin
ls -1 %{npdir}/plugins > %{npdir}/ls-plugins-before
ls -1 %{npdir}/plugins-root > %{npdir}/ls-plugins-root-before
ls -1 %{npdir}/plugins-scripts > %{npdir}/ls-plugins-scripts-before
make %{?_smp_mflags}
ls -1 %{npdir}/plugins > %{npdir}/ls-plugins-after
ls -1 %{npdir}/plugins-root > %{npdir}/ls-plugins-root-after
ls -1 %{npdir}/plugins-scripts > %{npdir}/ls-plugins-scripts-after

%pre
# Create `nagios' group on the system if necessary
%if %{isaix}
lsgroup %{npgrp} > /dev/null 2> /dev/null
if [ $? -eq 2 ] ; then
	mkgroup %{npgrp} || %nnmmsg Unexpected error adding group "%{npgrp}". Aborting install process.
fi
%endif
%if %{islinux}
getent group %{npgrp} > /dev/null 2> /dev/null
if [ $? -ne 0 ] ; then
	groupadd %{npgrp} || %nnmmsg Unexpected error adding group "%{npgrp}". Aborting install process.
fi
%endif

# Create `nagios' user on the system if necessary
%if %{isaix}
lsuser %{npusr} > /dev/null 2> /dev/null
if [ $? -eq 2 ] ; then
	useradd -d %{nphome} -c "%{npusr}" -g %{npgrp} %{npusr} || \
		%nnmmsg Unexpected error adding user "%{npusr}". Aborting install process.
fi
%endif
%if %{islinux}
getent passwd %{npusr} > /dev/null 2> /dev/null
if [ $? -ne 0 ] ; then
	useradd -r -d %{nphome} -c "%{npusr}" -g %{npgrp} %{npusr} || \
		%nnmmsg Unexpected error adding user "%{npusr}". Aborting install process.
fi
%endif

%install
rm -rf $RPM_BUILD_ROOT
make AM_INSTALL_PROGRAM_FLAGS="" DESTDIR=${RPM_BUILD_ROOT} install
make AM_INSTALL_PROGRAM_FLAGS="" DESTDIR=${RPM_BUILD_ROOT} install-packager
%find_lang %{name}
echo "%defattr(755,%{npusr},%{npgrp})" >> %{name}.lang
comm -13 %{npdir}/ls-plugins-before %{npdir}/ls-plugins-after | egrep -v "\.o$|^\." | gawk -v libexecdir=%{_libexecdir} '{printf( "%s/%s\n", libexecdir, $0);}' >> %{name}.lang
echo "%defattr(4555,root,%{npgrp})" >> %{name}.lang
comm -13 %{npdir}/ls-plugins-root-before %{npdir}/ls-plugins-root-after | egrep -v "\.o$|^\." | gawk -v libexecdir=%{_libexecdir} '{printf( "%s/%s\n", libexecdir, $0);}' >> %{name}.lang
echo "%defattr(755,%{npusr},%{npgrp})" >> %{name}.lang
comm -13 %{npdir}/ls-plugins-scripts-before %{npdir}/ls-plugins-scripts-after | egrep -v "\.o$|^\." | gawk -v libexecdir=%{_libexecdir} '{printf( "%s/%s\n", libexecdir, $0);}' >> %{name}.lang
echo "%{_libexecdir}/utils.pm" >> %{name}.lang
echo "%{_libexecdir}/utils.sh" >> %{name}.lang
echo "%{_libexecdir}/check_ldaps" >> %{name}.lang

sed -i '/libnpcommon/d' %{name}.lang
sed -i '/nagios-plugins.mo/d' %{name}.lang

%clean
rm -rf $RPM_BUILD_ROOT


%files -f %{name}.lang
%doc CODING COPYING FAQ INSTALL LEGAL README REQUIREMENTS SUPPORT THANKS
%doc ChangeLog
%if ! %{isaix}
%{_datadir}/locale/de/LC_MESSAGES/nagios-plugins.mo
%{_datadir}/locale/fr/LC_MESSAGES/nagios-plugins.mo
%endif

%changelog
* Thu Aug 18 2016 John Frickson jfrickson<@>nagios.com
- Removed references to the 'command.cfg' file, which no longer exists
* Mon May 23 2005 Sean Finney <seanius@seanius.net> - cvs head
- just include the nagios plugins directory, which will automatically include
  all generated plugins (which keeps the build from failing on systems that
  don't have all build-dependencies for every plugin)
* Tue Mar 04 2004 Karl DeBisschop <karl[AT]debisschop.net> - 1.4.0alpha1
- extensive rewrite to facilitate processing into various distro-compatible specs
* Tue Mar 04 2004 Karl DeBisschop <karl[AT]debisschop.net> - 1.4.0alpha1
- extensive rewrite to facilitate processing into various distro-compatible specs
