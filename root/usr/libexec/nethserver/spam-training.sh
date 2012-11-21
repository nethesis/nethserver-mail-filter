#!/bin/bash 

#
# NethServer -- spam-training.sh 
#
# Read a mail message from standard input and pass it to sa-learn.
#
# Copyright (C) 2012 Nethesis srl
#

exec >-

function log {
    local level=$1
    shift
    [ -x /usr/bin/logger ] && /usr/bin/logger -p "mail.${level}" "${PROG} (${USER})" $*;    
}

# sa_learn wrapper -- change the group id to amavis before executing
# sa-learn
function sa_learn {
   /usr/bin/sg amavis -c "/usr/bin/sa-learn $*"
}

export LANG=C

PROG=`basename $0`
USER=$1
ACTION=$2


# If defined spamtrainers user group, require that the current user is
# a member of it before going on.
sa_learn_group=`/usr/bin/getent group spamtrainers`
if [ $? -eq 0 ] && ! echo $sa_learn_group | \
    /bin/cut -d : -f 4 | \
    /bin/grep -q "\<${USER}\>"; then

    log debug "Not a member of 'spamtrainers' group. No sa-learn occurs."
    exit 0;
fi

# Ensure temporary spool file is deleted when the process terminates
TEMPFILE=`/bin/mktemp /var/tmp/spam-training.XXXXXXXXXXX`
trap "/bin/rm -f ${TEMPFILE}" EXIT SIGHUP SIGINT SIGTERM
#trap "logger -p mail.debug SIGINT" SIGINT
#trap "logger -p mail.debug SIGHUP" SIGHUP

/bin/cat <&0 >>${TEMPFILE} 

if [ $? -ne 0 ]; then
    log err "Could not write to ${TEMPFILE}"
    exit 2 
fi

if [ $ACTION == 'ham' ]; then
    sa_learn --ham ${TEMPFILE}
elif [ $ACTION == 'spam' ]; then
    sa_learn --spam ${TEMPFILE}
else 
    log err "Action '${ACTION}' is not recognized " 
    exit 3
fi

if [ $? -ne 0 ]; then
    log err "Message classification failed"
    log debug `env` `whoami` `id` `pwd`
    exit 1
fi 

log info "Message classified as ${ACTION}"

