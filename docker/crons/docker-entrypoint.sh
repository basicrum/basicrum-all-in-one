#!/usr/bin/env bash

crond -f -c $HOME/crontabs -L /proc/1/fd/2
