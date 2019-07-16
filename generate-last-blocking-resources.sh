#!/bin/bash
while true
do
    php $PWD/bin/console basicrum:calculate-last-blocking-resource
	echo "Do something; hit [CTRL+C] to stop!"
done