#!/bin/sh

PROJECT_ID=$1
QUERY_CURRENT_SPRINT=$2

#define the template.
cat  << EOF
PROJECT_ID=$PROJECT_ID
QUERY_CURRENT_SPRINT=$QUERY_CURRENT_SPRINT

EOF