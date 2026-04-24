#!/bin/bash

find_php() {
    for php in \
        /opt/cpanel/ea-php84/root/usr/bin/php \
        /opt/alt/php84/usr/bin/php \
        /opt/php84/usr/bin/php \
        /usr/local/bin/php \
        /usr/bin/php \
        /bin/php; do
        if [[ -x "$php" ]]; then
            echo "$php"
            return 0
        fi
    done
    return 1
}

PHP_BIN="$(find_php)" || {
    echo "Error: No PHP binary found."
    exit 1
}

exec "$PHP_BIN" "$@"
