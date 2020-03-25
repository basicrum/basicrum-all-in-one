#!/bin/bash
while true
do
    php $PWD/bin/console basicrum:last-blocking-resource:calculate
	echo "Do something; hit [CTRL+C] to stop!"
done