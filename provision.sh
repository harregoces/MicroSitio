#!/bin/bash
echo "Provisioning virtual machine..."

echo "Generating locales"
locale-gen en_IE.UTF-8 it_IT.UTF-8 es_ES.UTF-8 es_CO.UTF-8 > /dev/null 2>&1

echo "Installing python-software-properties"
apt-get install python-software-properties -y > /dev/null 2>&1

# Repositories first, so we just have to apt-get update once
echo "Adding Nginx repository"
#add-apt-repository ppa:nginx/stable -y > /dev/null 2>&1

echo "Adding percona repository"
wget https://repo.percona.com/apt/percona-release_0.1-4.$(lsb_release -sc)_all.deb > /dev/null 2>&1
dpkg -i percona-release_0.1-4.$(lsb_release -sc)_all.deb > /dev/null 2>&1
rm -rf percona-release_0.1-4.$(lsb_release -sc)_all.deb > /dev/null 2>&1

echo "Updating repositories"
apt-get update -y > /dev/null 2>&1
echo "Upgrading the system"
# https://github.com/chef/bento/issues/661#issuecomment-248136601
DEBIAN_FRONTEND=noninteractive apt-get -y -o Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" upgrade > /dev/null 2>&1

echo "Installing utilities"
apt-get install htop python2.7 vim git build-essential python-setuptools -y > /dev/null 2>&1

# PHP stuff
echo "Installing PHP dependencies"
apt-get install curl automake autoconf libtool libssl-dev shtool libpcre3-dev -y > /dev/null 2>&1

echo "Installing PHP"
apt-get install php7.0-common php7.0-dev php7.0-cli php7.0-fpm -y > /dev/null 2>&1

echo "Installing PHP extensions"
(pecl upgrade -f apcu_bc-beta) > /dev/null 2>&1
apt-get install php-igbinary php-apcu php-imagick php-intl php-mbstring php-memcache php-pear php-http php-xdebug php-xml php7.0-bcmath php7.0-cli php7.0-common php7.0-curl php7.0-dev php7.0-fpm php7.0-gd php7.0-intl php7.0-json php7.0-mbstring php7.0-mcrypt php7.0-mysql php7.0-opcache php7.0-readline php7.0-soap php7.0-xml php7.0-zip -y > /dev/null 2>&1

# xdebug.ini
#cp /var/provision/config/conf.d/xdebug.ini /etc/php/7.0/mods-available/

echo "Installing composer"
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer > /dev/null 2>&1

# Nginx stuff
echo "Installing Nginx"
apt-get install nginx -y > /dev/null 2>&1

#echo "Configuring Nginx"
#rm /etc/nginx/sites-available/default
#cp /vhost_ngnix /etc/nginx/sites-available/default

echo "Restarting Nginx"
service nginx restart > /dev/null 2>&1

# DB stuff
if [ ! -f /var/log/dbinstalled ];
then
    echo "Setting root password for mysql percona"
    debconf-set-selections <<< 'percona-server-server-5.5 percona-server-server/root_password password d1ab0l1k'
    debconf-set-selections <<< 'percona-server-server-5.5 percona-server-server/root_password_again password d1ab0l1k'

    echo "Installing Percona client and server"
    apt-get install percona-server-server-5.5 percona-server-client-5.5 -y > /dev/null 2>&1

    echo "Setting up user and DB"
    echo "CREATE USER 'eshop'@'%' IDENTIFIED BY 'd1ab0l1k'" | mysql -uroot -pd1ab0l1k
    echo "CREATE USER 'eshop'@'localhost' IDENTIFIED BY 'd1ab0l1k'" | mysql -uroot -pd1ab0l1k
    echo "CREATE DATABASE eshop_production_rec" | mysql -uroot -pd1ab0l1k
    echo "GRANT ALL ON eshop_production_rec.* TO 'eshop'@'%'" | mysql -uroot -pd1ab0l1k
    echo "GRANT ALL ON eshop_production_rec.* TO 'eshop'@'localhost'" | mysql -uroot -pd1ab0l1k
    echo "CREATE DATABASE admin_production" | mysql -uroot -pd1ab0l1k
    echo "GRANT ALL ON admin_production.* TO 'eshop'@'%'" | mysql -uroot -pd1ab0l1k
    echo "GRANT ALL ON admin_production.* TO 'eshop'@'localhost'" | mysql -uroot -pd1ab0l1k
    echo "FLUSH PRIVILEGES" | mysql -uroot -pd1ab0l1k
    touch /var/log/dbinstalled
fi

echo "Restarting PHP-FPM"
service php7.0-fpm restart > /dev/null 2>&1
