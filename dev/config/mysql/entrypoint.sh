#!/bin/bash
#
# Alternate entrypoint script to run additional initialization on every startup
#
# References:
#   https://github.com/docker-library/mariadb/issues/284#issuecomment-575349157
#   https://github.com/docker-library/mysql/blob/aaecc90a37/5.6/docker-entrypoint.sh

set -eo pipefail

source "$(which docker-entrypoint.sh)"

mysql_note "Custom entrypoint script for MySQL Server ${MYSQL_VERSION} started."

mysql_check_config "$@"
# Load various environment variables
docker_setup_env "$@"
docker_create_db_directories

# If container is started as root user, restart as dedicated mysql user
if [ "$(id -u)" = "0" ]; then
    mysql_note "Switching to dedicated user 'mysql'"
    exec gosu mysql "$BASH_SOURCE" "$@"
fi

if [ -z "$DATABASE_ALREADY_EXISTS" ]; then
    # there's no database, so it needs to be initialized
    docker_verify_minimum_env

    # check dir permissions to reduce likelihood of half-initialized database
    ls /docker-entrypoint-initdb.d/ > /dev/null

    docker_init_database_dir "$@"

    mysql_note "Starting temporary server"
    docker_temp_server_start "$@"
    mysql_note "Temporary server started."

    docker_setup_db
    docker_process_init_files /docker-entrypoint-initdb.d/*

    mysql_note "Stopping temporary server"
    docker_temp_server_stop
    mysql_note "Temporary server stopped"

    echo
    mysql_note "MySQL init process done. Ready for start up."
    echo
elif test -n "$(shopt -s nullglob; echo /always-initdb.d/*)"; then
    # Database exists; run always-run hooks if they exist
    mysql_note "Starting temporary server"
    docker_temp_server_start "$@"
    mysql_note "Temporary server started."

    docker_process_init_files /always-initdb.d/*

    mysql_note "Stopping temporary server"
    docker_temp_server_stop
    mysql_note "Temporary server stopped"

    echo
    mysql_note "MySQL init process done. Ready for start up."
    echo
fi

exec "$@"