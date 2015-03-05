Summary: Enforces anti-spam and anti-virus checks on any message entering the mail system.
Name: nethserver-mail-filter
Version: 1.2.1
Release: 1%{?dist}
License: GPL
URL: %{url_prefix}/%{name} 
Source0: %{name}-%{version}.tar.gz
BuildArch: noarch
%define policyd_spf_dir postfix-policyd-spf-perl-2.010

Requires: nethserver-mail-common, nethserver-antivirus
Requires: perl-Mail-SPF >= 2.007
Requires: perl-Sys-Hostname-Long

# Additional archive formats support for amavis (EPEL) Refs #2093:
# optional packages: lha, unrar, ripole

# required by amavis
Requires: cronie, pax

BuildRequires: perl
BuildRequires: nethserver-devtools

%description
Configures postfix to filter SMTP sessions and mail messages through 
- amavisd-new (spamassassin, clamav)
- Mail::SPF (optional)

%prep
%setup

%build
POLICYD_SPF_DIR=%{policyd_spf_dir}
perl createlinks
mkdir -p root/usr/libexec/nethserver
mv -v $POLICYD_SPF_DIR/postfix-policyd-spf-perl root/usr/libexec/nethserver/
mkdir -p root${RPM_DOC_DIR}
mv -v $POLICYD_SPF_DIR root${RPM_DOC_DIR}

%pre
# ensure spfd user exists:
for FILTER_USER in spfd; do
    if ! id $FILTER_USER >/dev/null 2>&1 ; then
       useradd -r $FILTER_USER
    fi
done

%install
POLICYD_SPF_DIR=%{policyd_spf_dir}
rm -rf $RPM_BUILD_ROOT
(cd root; find . -depth -print | cpio -dump $RPM_BUILD_ROOT)
%{genfilelist} $RPM_BUILD_ROOT > %{name}-%{version}-filelist
echo "%doc COPYING" >> %{name}-%{version}-filelist
echo "%docdir $RPM_DOC_DIR/${POLICYD_SPF_DIR}" >> %{name}-%{version}-filelist

%clean
rm -rf $RPM_BUILD_ROOT

%post

%files -f %{name}-%{version}-filelist
%defattr(-,root,root)

%changelog
* Tue Dec 09 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.1-1.ns6
- Support  Sanesecurity Foxhole - Feature #2897 [NethServer]

* Tue Oct 07 2014 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.2.0-1.ns6
- Differentiate Postfix syslog_name parameters - Enhancement #2784
- Relax Postix restrictions for whitelisted senders - Enhancement #2768

* Wed Mar 19 2014 Davide Principi <davide.principi@nethesis.it> - 1.1.6-1.ns6
- Let amavisd PASS unchecked content - Enhancement #2689 [NethServer]

* Mon Mar 10 2014 Davide Principi <davide.principi@nethesis.it> - 1.1.5-1.ns6
- Antivirus dashboard widget - Enhancement #2686 [NethServer]
- Wrong default thresholds for antispam - Enhancement #2681 [NethServer]
- Mail filter: remove clamav warning - Enhancement #2678 [NethServer]

* Wed Dec 18 2013 Davide Principi <davide.principi@nethesis.it> - 1.1.4-1.ns6
- Stale ClamAV DB warning - Bug #2351 [NethServer]

* Thu Oct 17 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.3-1.ns6
- Ignore spam training when moving a message to Trash #2252

* Mon Sep 02 2013 Davide Principi <davide.principi@nethesis.it> - 1.1.2-1.ns6
- amavisd-new 2.8.0 from EPEL - Enhancement #2093 [NethServer]

* Tue Jun 25 2013 Giacomo Sanchietti <giacomo.sanchietti@nethesis.it> - 1.1.1-1.ns6
- Adjust bayes scores  #1909

* Tue Apr 30 2013 Davide Principi <davide.principi@nethesis.it> - 1.1.0-1.ns6
- Full automatic package install/upgrade/uninstall support #1870 #1872 #1874
- Spam settings migration #1820
- White/Black lists migration #1796

* Tue Mar 19 2013 Davide Principi <davide.principi@nethesis.it> - 1.0.1-1.ns6
- spam-training.sh: fixed wrong bash syntax to close stdout descriptor. Refs #1656 
- *.spec: use url_prefix macro in URL tag; removed nethserver-devtools specific version requirement; fixed Release tag expansion. Refs #1654
