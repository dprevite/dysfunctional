#!/bin/bash
set -e

# Get the host user/group IDs from environment or use defaults
HOST_UID=${HOST_UID:-1000}
HOST_GID=${HOST_GID:-1000}

# Get current www-data UID/GID
CURRENT_UID=$(id -u www-data)
CURRENT_GID=$(id -g www-data)

# Only modify if IDs don't match and we're in development mode
if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ]; then
    echo "Development mode detected..."

    if [ "$CURRENT_UID" != "$HOST_UID" ] || [ "$CURRENT_GID" != "$HOST_GID" ]; then
        echo "Updating www-data UID:GID from $CURRENT_UID:$CURRENT_GID to $HOST_UID:$HOST_GID"

        # Update group ID
        if [ "$CURRENT_GID" != "$HOST_GID" ]; then
            groupmod -o -g "$HOST_GID" www-data
        fi

        # Update user ID
        if [ "$CURRENT_UID" != "$HOST_UID" ]; then
            usermod -o -u "$HOST_UID" www-data
        fi

        echo "UID/GID updated successfully"
    else
        echo "UID/GID already matches host ($HOST_UID:$HOST_GID)"
    fi

    # Ensure Laravel directories have correct permissions
    echo "Fixing Laravel directory permissions..."
    chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
    chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true
fi

# If we're root, drop down to www-data for the actual command
if [ "$(id -u)" = "0" ]; then
    echo "Switching to www-data user..."
    exec gosu www-data "$@"
else
    exec "$@"
fi