#!/bin/bash 

#
# NethServer -- spam-training.sh 
#
# Read a mail message from standard input and pass it to sa-learn.
# This script is executed by dovecot as vmail user.
#
# Copyright (C) 2012 Nethesis srl
#

# Close STDOUT descriptor
exec >-

PROG=`basename $0`
USER=$1
ACTION=$2

function log {
    local level=$1
    shift
    [ -x /usr/bin/logger ] && /usr/bin/logger -i -t "${PROG}/${USER}" -p "mail.${level}" $*;    
}


# If defined spamtrainers user group, require that the current user is
# a member of it before going on.
sa_learn_group=`/usr/bin/getent group spamtrainers`
if [ $? -eq 0 ] && ! echo $sa_learn_group | \
    /bin/cut -d : -f 4 | \
    /bin/grep -q "\<${USER}\>"; then
    log debug "Not a member of 'spamtrainers' group. Nothing to do."
    exit 0;
fi

if ! [ $ACTION == 'ham' ] && ! [ $ACTION == 'spam' ] ; then
    log err "Action '${ACTION}' is not recognized" 
    exit 3
fi

/usr/sbin/sendmail -F 'spam-training.sh script' -r root@`hostname` ${USER}+${ACTION}@spamtrain.nh && log info "Message enqueued as ${ACTION}"

