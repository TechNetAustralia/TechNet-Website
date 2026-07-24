#!/bin/zsh -l
# Double-click this to start the local TechNet Australia sandbox.
# No Terminal typing required — this handles Docker + wp-env for you.

cd "$(dirname "$0")"

if ! command -v wp-env &> /dev/null; then
	echo "wp-env isn't installed yet."
	echo "Open Terminal and run this once, then try this button again:"
	echo ""
	echo "  npm install -g @wordpress/env"
	echo ""
	read "REPLY?Press enter to close this window..."
	exit 1
fi

echo "Making sure Docker Desktop is running..."
open -a Docker

echo "Waiting for Docker to be ready (this can take a minute the first time)..."
docker_ready=0
for i in {1..45}; do
	if docker info &> /dev/null; then
		docker_ready=1
		break
	fi
	sleep 2
done

if [ "$docker_ready" -ne 1 ]; then
	echo ""
	echo "Docker Desktop didn't finish starting in time."
	echo "Open the Docker Desktop app yourself, wait for the whale icon"
	echo "in the menu bar to stop animating, then try this button again."
	read "REPLY?Press enter to close this window..."
	exit 1
fi

echo ""
echo "Starting the TechNet Australia sandbox..."
wp-env start
start_status=$?

echo ""
if [ "$start_status" -eq 0 ]; then
	echo "Opening http://localhost:8888 ..."
	open "http://localhost:8888"
else
	echo "Something went wrong starting the sandbox — see the messages above."
	echo "If it mentions a port already being used, try the Stop Site button first, then Start Site again."
fi

read "REPLY?Press enter to close this window..."
