#!/bin/bash

# the script file name to run
script_name="main"

# checking if the script is already running
process_running=$(ps aux | grep $script_name | grep -v "grep $script_name")

if [ -n "$process_running" ]; then
	echo $process_running
else
	# checking if the lock file exists
	if [ -f ".naruconv_lock" ]; then
		# deleting the lock file to safely run a script
		rm -rf .naruconv_lock
	fi
fi

# Running a script in the background
# Writing output from the script to session.log if there is any issues
./$script_name >> session.log 2>&1 &
