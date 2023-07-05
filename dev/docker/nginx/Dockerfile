FROM nginx:latest

RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data


RUN touch /var/run/nginx.pid \
 && chown -R www-data:www-data \
    /var/run/nginx.pid \
    /var/cache/nginx/

USER www-data