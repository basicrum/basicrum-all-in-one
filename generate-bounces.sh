#!/bin/bash
while true
do
    php $PWD/bin/console basicrum:regenerate-bounces
	echo "Do something; hit [CTRL+C] to stop!"
done