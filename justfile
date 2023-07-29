set dotenv-load

run:
    php -S 0.0.0.0:8080 -t example-app/public

check:
    vendor/bin/psalm

