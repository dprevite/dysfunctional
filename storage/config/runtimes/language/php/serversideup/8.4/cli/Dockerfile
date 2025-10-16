FROM serversideup/php:8.4-cli

ENV SHOW_WELCOME_MESSAGE=false

# Switch to root so we can do root things
USER root

# Save the build arguments as a variable
ARG USER_ID
ARG GROUP_ID

# Use the build arguments to change the UID
# and GID of www-data
RUN docker-php-serversideup-set-id www-data $USER_ID:$GROUP_ID

WORKDIR /app

USER www-data

COPY --chown=www-data:www-data . /app

CMD ["php", "entrypoint.php"]
