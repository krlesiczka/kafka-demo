FROM php:7-cli
RUN apt-get update && apt-get install -y libmcrypt-dev wget python mc

WORKDIR /tmp

ENV LIBRDKAFKA_VERSION=0.9.3

RUN wget https://github.com/edenhill/librdkafka/archive/v${LIBRDKAFKA_VERSION}.tar.gz && \
    tar -xvf v${LIBRDKAFKA_VERSION}.tar.gz && \
    cd librdkafka-${LIBRDKAFKA_VERSION} && \
    ./configure && make && make install && ldconfig && cd /tmp && \
    rm -rf librdkafka-${LIBRDKAFKA_VERSION} v${LIBRDKAFKA_VERSION}.tar.gz

RUN docker-php-ext-install mbstring tokenizer && \
    pecl install rdkafka && \
    rm -rf /tmp/pear && \
    echo "extension=rdkafka.so" > /usr/local/etc/php/conf.d/rdkafka.ini

RUN php -i

CMD ["php","-S","0.0.0.0:8080","-t", "/var/www/html"]