FROM ubuntu:24.04

LABEL maintainer="Taylor Otwell"

ARG WWWGROUP
ARG NODE_VERSION=22
ARG MYSQL_CLIENT="mysql-client"
ARG POSTGRES_VERSION=17

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV SUPERVISOR_PHP_COMMAND="/usr/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80"
ENV SUPERVISOR_PHP_USER="sail"
ENV PATH="$PATH:/root/.composer/vendor/bin"

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::BrokenProxy    true;" >> /etc/apt/apt.conf.d/99custom

RUN apt-get update && apt-get upgrade -y
RUN mkdir -p /etc/apt/keyrings
RUN apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python3 dnsutils librsvg2-bin fswatch ffmpeg nano 
RUN curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0xb8dc7e53946656efbce4c1dd71daeaab4ad4cab6' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null
RUN echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" > /etc/apt/sources.list.d/ppa_ondrej_php.list
RUN apt-get update
RUN apt-get install -y php8.4-cli php8.4-dev \
       php8.4-pgsql php8.4-sqlite3 php8.4-gd \
       php8.4-curl php8.4-mongodb \
       php8.4-imap php8.4-mysql php8.4-mbstring \
       php8.4-xml php8.4-zip php8.4-bcmath php8.4-soap \
       php8.4-intl php8.4-readline \
       php8.4-ldap \
       php8.4-msgpack php8.4-igbinary php8.4-redis php8.4-swoole \
       php8.4-memcached php8.4-pcov php8.4-imagick php8.4-xdebug
RUN curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list
RUN apt-get update
RUN apt-get install -y nodejs
RUN npm install -g npm
RUN npm install -g pnpm
RUN npm install -g bun
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /etc/apt/keyrings/yarn.gpg >/dev/null
RUN echo "deb [signed-by=/etc/apt/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list
RUN curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null
RUN echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt noble-pgdg main" > /etc/apt/sources.list.d/pgdg.list
RUN apt-get update
RUN apt-get install -y yarn
RUN apt-get install -y $MYSQL_CLIENT
RUN apt-get install -y postgresql-client-$POSTGRES_VERSION
# RUN pecl install grpc protobuf
# # RUN docker-php-ext-enable grpc protobuf
# RUN echo "extension=grpc.so" > /etc/php/8.4/cli/conf.d/99-grpc.ini && \
#     echo "extension=protobuf.so" > /etc/php/8.4/cli/conf.d/99-protobuf.ini

# gRPC tooling libs
# RUN apt-get install -y libgrpc-dev libpng-dev zlib1g-dev libicu-dev libonig-dev libxml2-dev libprotobuf-dev protobuf-compiler

# COPY ./.docker/php-extensions/*.so /usr/lib/php/20240924/
RUN mkdir -p /tmp/php-extensions
RUN curl -L https://github.com/rxcod9/laravel-grpc-example/releases/download/v1.0.0/php8.4-grpc.so.tar.gz \
    | tar -xz -C /tmp/php-extensions
RUN curl -L https://github.com/rxcod9/laravel-grpc-example/releases/download/v1.0.0/php8.4-protobuf.so.tar.gz \
    | tar -xz -C /tmp/php-extensions
RUN cp -r /tmp/php-extensions/*.so /usr/lib/php/20240924/
RUN rm -rf /tmp/php-extensions
RUN echo "extension=grpc.so" > /etc/php/8.4/cli/conf.d/99-grpc.ini && \
    echo "extension=protobuf.so" > /etc/php/8.4/cli/conf.d/99-protobuf.ini

# Copy gRPC binaries and extension from grpc-base
# COPY --from=grpc-base /github/grpc/cmake/build/third_party/protobuf/protoc /usr/local/bin/protoc
# COPY --from=grpc-base /github/grpc/cmake/build/grpc_php_plugin /usr/local/bin/protoc-gen-grpc
# COPY --from=grpc-base /usr/local/lib/php/extensions/no-debug-non-zts-20190902/grpc.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902/grpc.so

# # Enable grpc
# RUN echo "extension=grpc.so" > /etc/php/8.4/cli/conf.d/20-grpc.ini


RUN apt-get -y autoremove
RUN apt-get clean
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.4

RUN userdel -r ubuntu
RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

COPY ./.docker/start-container /usr/local/bin/start-container
COPY ./.docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./.docker/php.ini /etc/php/8.4/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 80/tcp

ENTRYPOINT ["start-container"]
