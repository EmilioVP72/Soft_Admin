#!/bin/bash
set -e

sudo chown -R emilio:devteam /app/vendor /app/storage /app/bootstrap/cache

exec "$@"