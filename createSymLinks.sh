#!/bin/bash

[ $# -eq 1 ] || { echo "Usage: createSymLinks /path/to/joomla/installation"; exit 1; }
[ -d $1 ] || { echo "Usage: createSymLinks /path/to/joomla/installation"; exit 1; }

JOOMLA_DIR=${1%/}
DIR_OF_THIS_FILE="$( cd "$( dirname "$0" )" && pwd )"

# backend
[ -d "${JOOMLA_DIR}/administrator/components/com_events" ] && rm -rf "${JOOMLA_DIR}/administrator/components/com_events"
ln -s "${DIR_OF_THIS_FILE}/administrator" "${JOOMLA_DIR}/administrator/components/com_events"
ln -s "${DIR_OF_THIS_FILE}/events.xml" "${JOOMLA_DIR}/administrator/components/com_events/events.xml"
find "${DIR_OF_THIS_FILE}/administrator" -type d -exec chmod 755 {} +
find "${DIR_OF_THIS_FILE}/administrator" -type f -exec chmod 644 {} +

# frontend
[ -d "${JOOMLA_DIR}/components/com_events" ] && rm -rf "${JOOMLA_DIR}/components/com_events"
ln -s "${DIR_OF_THIS_FILE}/site" "${JOOMLA_DIR}/components/com_events"
find "${DIR_OF_THIS_FILE}/site" -type d -exec chmod 755 {} +
find "${DIR_OF_THIS_FILE}/site" -type f -exec chmod 644 {} +

# media
[ -d "${JOOMLA_DIR}/media/com_events" ] && rm -rf "${JOOMLA_DIR}/media/com_events"
ln -s "${DIR_OF_THIS_FILE}/media" "${JOOMLA_DIR}/media/com_events"
find "${DIR_OF_THIS_FILE}/media" -type d -exec chmod 755 {} +
find "${DIR_OF_THIS_FILE}/media" -type f -exec chmod 644 {} +

