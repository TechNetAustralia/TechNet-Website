#!/bin/zsh -l
# Double-click this to stop the local TechNet Australia sandbox at the end
# of the day. Nothing you've added is lost — it's paused, not deleted.

cd "$(dirname "$0")"

if ! command -v wp-env &> /dev/null; then
	echo "wp-env isn't installed — nothing to stop."
	read "REPLY?Press enter to close this window..."
	exit 1
fi

echo "Stopping the TechNet Australia sandbox..."
wp-env stop

echo ""
echo "Done. Your content and settings are saved for next time."
read "REPLY?Press enter to close this window..."
