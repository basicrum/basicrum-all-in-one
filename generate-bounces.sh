#!/bin/bash
while true
do
    php $PWD/bin/console basicrum:visit:generate
	echo "Do something; hit [CTRL+C] to stop!"
done