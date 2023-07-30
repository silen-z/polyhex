set dotenv-load

run:
    php -S 0.0.0.0:8080 -t example-app/public

run-roadrunner:
    ./rr -c example-app/rr.yaml serve

run-reactphp:
    php example-app/reactphp_entry.php

check:
    vendor/bin/psalm

