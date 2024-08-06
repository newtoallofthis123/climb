#!/bin/bash

if ! docker run --rm hello-world >/dev/null 2>&1; then
    if [ "$(id -u)" -ne 0 ]; then
        echo "Docker requires sudo privileges. Please run with 'sudo'."
        exit 1
    fi
    sudo docker build -t climbs_ui .
    sudo docker run -p 8080:80 climbs_ui
    exit 0
fi

docker build -t climbs_ui .
docker run -p 8080:80 climbs_ui