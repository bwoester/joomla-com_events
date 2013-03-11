#!/bin/bash

[[ -f "com_events.zip" ]] && rm -f "com_events.zip"

zip -r -q com_events administrator/ media/ site/ events.xml