#!/bin/bash

# the script file name to run
script_name="main"
filelock_name=".naruconv.lock"

# checking if the script is already running
process_running=$(ps aux | egrep "$script_name$" | grep -v grep)

if [ -n "$process_running" ]; then
	echo $process_running
else
	# checking if the lock file exists
	if [ -f "$filelock_name" ]; then
		# deleting the lock file to safely run a script
		rm -rf $filelock_name
		echo "*** Starting a compiler..."
	fi
fi

# Running a script in the background
# Writing output from the script to session.log if there is any issues
./$script_name >> session.log 2>&1 &
