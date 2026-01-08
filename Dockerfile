FROM --platform=linux/arm64 ymirapp/arm-php-runtime:php-85

ENTRYPOINT []

CMD ["/bin/sh", "-c", "/opt/bootstrap"]

COPY . /var/task
