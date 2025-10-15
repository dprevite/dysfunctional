FROM dunglas/frankenphp AS base

COPY Caddyfile /etc/frankenphp/Caddyfile
COPY . /app

RUN install-php-extensions \
	pdo_pgsql \
	gd \
	intl \
	zip \
	opcache

# Install Docker CLI
RUN apt-get update && \
	apt-get install -y \
		ca-certificates \
		curl \
		gnupg \
		git \
		unzip \
		gosu && \
	install -m 0755 -d /etc/apt/keyrings && \
	curl -fsSL https://download.docker.com/linux/debian/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg && \
	chmod a+r /etc/apt/keyrings/docker.gpg && \
	echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/debian bookworm stable" > /etc/apt/sources.list.d/docker.list && \
	apt-get update && \
	apt-get install -y docker-ce-cli docker-compose-plugin && \
	apt-get clean && \
	rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install NVM and Node.js
ENV NODE_VERSION=24
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash && \
	export NVM_DIR="/config/nvm" && \
	. "$NVM_DIR/nvm.sh" && \
	nvm install ${NODE_VERSION} && \
	nvm alias default ${NODE_VERSION} && \
	nvm use default

# Add NVM to PATH
ENV NVM_DIR=/config/nvm
ENV NODE_PATH=/config/nvm/versions/node/v20.11.0/lib/node_modules
ENV PATH=/config/nvm/versions/node/v20.11.0/bin:$PATH

# Add Laravel vendor/bin to PATH
ENV PATH=/app/vendor/bin:$PATH

# Development stage
FROM base AS development

RUN apt-get update && \
	apt-get install -y \
		tree \
		procps && \
	apt-get clean && \
	rm -rf /var/lib/apt/lists/* && \
	echo 'alias ll="ls -al"' >> /etc/bash.bashrc && \
	echo 'alias ll="ls -al"' >> /root/.bashrc && \
	mkdir -p /var/www && \
	echo 'alias ll="ls -al"' >> /var/www/.bashrc

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Ensure Laravel directories exist with proper permissions
RUN mkdir -p /app/storage/logs /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views /app/bootstrap/cache && \
	chown -R www-data:www-data /app/storage /app/bootstrap/cache && \
	chmod -R 775 /app/storage /app/bootstrap/cache

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy Xdebug configuration
COPY .container/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN git config --global --add safe.directory /app

WORKDIR /app

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]

# Production stage
FROM base AS production

WORKDIR /app

RUN rm -rf .git .github tests

USER www-data

# Optimize for production
RUN composer install --no-dev --optimize-autoloader && \
	php artisan config:cache && \
	php artisan route:cache && \
	php artisan view:cache
