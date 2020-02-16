#!/bin/bash

#!/usr/bin/env bash

set -e
set -x

#php $PWD/bin/console -vvv basicrum:beacon:bundle-raw
php $PWD/bin/console -vvv basicrum:beacon:import-bundle
php $PWD/bin/console -vvv basicrum:beacon:archive-bundle
