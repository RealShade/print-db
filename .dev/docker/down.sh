#!/bin/bash

source .env
docker compose --project-name $CONTAINER down
