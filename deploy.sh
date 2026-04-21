#!/usr/bin/env bash
# Deploy rgeometry-wp to the Cloudways sandbox.
#
# Usage:   ./deploy.sh [host] [user] [remote-theme-dir]
# Default targets match the sandbox described in README.
#
# The script packages the theme (excluding .git and the local dev tarball),
# sftp-uploads the archive, extracts it server-side, and fixes perms so nginx
# can serve every file (644 for files, 755 for directories). Skipping the chmod
# step was how the first deploy shipped an invisible theme.
set -euo pipefail

HOST="${1:-138.197.111.236}"
USER="${2:-lovable}"
REMOTE_DIR="${3:-public_html/wp-content/themes/rgeometry-wp}"

HERE="$(cd "$(dirname "$0")" && pwd)"
ARCHIVE="/tmp/rgeometry-wp-deploy.tar.gz"

echo "> packaging $HERE"
tar --exclude='.git' --exclude='deploy.sh' --exclude='*.tar.gz' -czf "$ARCHIVE" -C "$(dirname "$HERE")" "$(basename "$HERE")"

echo "> uploading to $USER@$HOST:~/tmp/"
scp -o StrictHostKeyChecking=no "$ARCHIVE" "$USER@$HOST:~/tmp/"

echo "> extracting + chmodding remote"
ssh -o StrictHostKeyChecking=no "$USER@$HOST" bash -s <<'REMOTE_EOF'
set -euo pipefail
cd ~/public_html/wp-content/themes
rm -rf rgeometry-wp
tar -xzf ~/tmp/rgeometry-wp-deploy.tar.gz -C .
find rgeometry-wp -type d -exec chmod 755 {} \;
find rgeometry-wp -type f -exec chmod 644 {} \;
echo "extracted $(find rgeometry-wp -type f | wc -l) files"
REMOTE_EOF

echo "> done"
