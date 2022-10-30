#! /bin/bash

PHP_VERSION="8.1.12"
PHP_PATH="/usr/local/php"
TMP_DIR="$(mktemp -d)"
TMP_PATH_PHP="${TMP_DIR}/php-src-php-${PHP_VERSION}"
SOURCE_DIR="$(dirname "$(realpath "$0")")/.."

skip_apt=false
skip_composer=false
skip_php=false
positionals=""
while (("$#")); do
    case "$1" in
    --skip-apt)
        skip_apt=true
        shift
        ;;
    --skip-composer)
        skip_composer=true
        shift
        ;;
    --skip-php)
        skip_php=true
        shift
        ;;
    -* | --*) # unsupported arguments
        echo "ERROR: unsupported $1" >&2
        exit 1
        ;;
    *)
        positionals="$positionals $1"
        shift
        ;;
     esac
done
eval set -- "$positionals"

# SGDInstitute/enterprise requires the pcntl extension which calls for a custom build of PHP

function build_ext() {
    cd "${TMP_PATH_PHP}/ext/$1"
    phpize
    ./configure
    sudo make
    sudo make install
}

function build_xdebug() {
    cd "${TMP_DIR}"
    XDEBUG_VERSION="3.1.5"
    curl -L "https://github.com/xdebug/xdebug/archive/refs/tags/${XDEBUG_VERSION}.zip" -o xdebug.zip
    unzip xdebug.zip
    cd "xdebug-${XDEBUG_VERSION}"
    phpize
    ./configure
    sudo make
    sudo make install
}

function build_extenstions() {
    build_ext bcmath
    build_ext curl
    build_ext exif
    build_ext gd
    build_ext openssl
    build_ext pcntl
    build_ext sodium
    build_ext zip
}

function build_php() {
    cd "${TMP_PATH_PHP}"
    ./buildconf --force
    ./configure \
        --prefix=/usr/local/php/${PHP_VERSION}-enterprise \
        --enable-bcmath \
        --with-curl \
        --enable-exif \
        --enable-gd \
        --with-openssl \
        --enable-pcntl \
        --with-sodium \
        --with-system-ciphers \
        --enable-xdebug \
        --with-zip \
        --with-zlib
    sudo INSTALL_ROOT=/ DESTDIR=/ make -j$(nproc) install

    # fix symbolic link
    rm -rf /usr/local/php/current
    ln -s /usr/local/php/${PHP_VERSION}-enterprise /usr/local/php/current
}

function install_composer() {
    cd "${TMP_DIR}"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    php composer-setup.php
    php -r "unlink('composer-setup.php');"
    sudo mv composer.phar /usr/local/bin/composer
}

if [ "$skip_apt" = false ] ; then
    sudo apt update
    sudo apt install -y libzip-dev
fi

if [ "$skip_php" = false ] ; then
    curl -L "https://github.com/php/php-src/archive/refs/tags/php-${PHP_VERSION}.zip" -o "/tmp/php-${PHP_VERSION}.zip"
    mkdir -p "${PHP_PATH}"
    unzip "/tmp/php-${PHP_VERSION}.zip" -d "${TMP_DIR}"

    build_php
    build_extenstions
    build_xdebug

    # copy php.ini
    cd "${SOURCE_DIR}"
    sudo cp .devcontainer/enterprise.php.ini `php --ini | grep "Path:" | sed -e "s|.*:\s*||"`/php.ini
fi

if [ "$skip_composer" = false ] ; then
    install_composer
fi

# cleanup
# rm -rf "${TMP_DIR}"
echo "${PHP_VERSION}-enterprise"
echo "${TMP_DIR}"
