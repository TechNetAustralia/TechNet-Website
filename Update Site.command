#!/bin/zsh -l
# Double-click this any time to pull the latest changes from GitHub.
# Equivalent to "Fetch origin" + "Pull origin" in GitHub Desktop, if you'd
# rather use that instead — this button is just a Terminal-free shortcut.

cd "$(dirname "$0")"

if ! command -v git &> /dev/null; then
	echo "git isn't installed — install Xcode Command Line Tools or Git first."
	read "REPLY?Press enter to close this window..."
	exit 1
fi

echo "Checking out main and pulling the latest changes..."
git checkout main
git pull origin main
pull_status=$?

echo ""
if [ "$pull_status" -ne 0 ]; then
	echo "The pull didn't complete cleanly — see the messages above."
	echo "If it mentions local changes or a conflict, ask Claude Code for help"
	echo "rather than guessing at git commands here."
else
	echo "Up to date."
	echo ""
	echo "If the sandbox is already running, hard-refresh your browser"
	echo "(Cmd+Shift+R) at http://localhost:8888 to see the changes —"
	echo "a normal refresh can show a cached, out-of-date page."
fi

read "REPLY?Press enter to close this window..."
