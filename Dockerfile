FROM alpine:3.14

COPY ./ /app/
COPY ./.devops/docker/filesystem/ /

RUN apk add bash supervisor git openssl nginx php8-fpm php8-json php8-cli php8-sockets && \
	apk add php8-openssl php8-fileinfo php8-mbstring php8-gd php8-pecl-mongodb php-phar && \
	adduser -S www-data -G www-data && \
	cd /etc/nginx && git clone https://github.com/Qybercom/NXHost.git && mv ./NXHost ./nxhost && \
	chown -R www-data:www-data /var/lib/nginx && \
	mkdir /var/run/php && chown -R www-data:www-data /var/run/php && \
    ln -s /usr/bin/php8 /bin/php && \
    cd /root && wget --inet4-only https://getcomposer.org/installer && php installer && alias composer='php composer.phar' && \
	chown -R www-data:www-data /app && chmod -R 770 /app

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]