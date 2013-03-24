#!/bin/bash

[ $# -eq 1 ] || { echo "Usage: removeSymLinks /path/to/joomla/installation"; exit 1; }
[ -d $1 ] || { echo "Usage: removeSymLinks /path/to/joomla/installation"; exit 1; }

JOOMLA_DIR=${1%/}
DIR_OF_THIS_FILE="$( cd "$( dirname "$0" )" && pwd )"

# backend
[ -h "${JOOMLA_DIR}/administrator/components/com_events/events.xml" ] && rm "${JOOMLA_DIR}/administrator/components/com_events/events.xml"
[ -h "${JOOMLA_DIR}/administrator/components/com_events" ] && rm "${JOOMLA_DIR}/administrator/components/com_events"

# frontend
[ -h "${JOOMLA_DIR}/components/com_events" ] && rm "${JOOMLA_DIR}/components/com_events"

# media
[ -h "${JOOMLA_DIR}/media/com_events" ] && rm "${JOOMLA_DIR}/media/com_events"
