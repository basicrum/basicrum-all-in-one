#!/bin/sh
set -e

if [ "www-data" != "$(stat -c '%U' .)" ]; then
  chown www-data:www-data -R .
fi
