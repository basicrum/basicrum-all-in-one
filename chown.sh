#!/bin/sh
set -e

if [ "root" != "$(stat -c '%U' .)" ]; then
  chown root:root -R .
fi
