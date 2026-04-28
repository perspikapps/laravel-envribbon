#!/bin/sh

### Add ssh-keyscan to known_hosts
mkdir -p /home/vscode/.ssh
chmod 700 /home/vscode/.ssh
ssh-keyscan github.com >>/home/vscode/.ssh/known_hosts

### Ensure correct access rights
workspace_dir="${containerWorkspaceFolder:-.}"
sudo chown -Rf vscode:vscode "$workspace_dir"
sudo chmod 755 "$workspace_dir"
sudo find "$workspace_dir" -mindepth 1 -type d -exec chmod 755 {} +
