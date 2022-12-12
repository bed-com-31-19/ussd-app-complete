FROM php:8.1.2-apache
COPY . .
CMD [ "php", "-S", "0.0.0.0:8080" ]