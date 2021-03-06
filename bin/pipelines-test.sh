#!/usr/bin/env bash
set -e
set -x

export MODULE_DIR=`pwd`
export M2SETUP_VERSION=latest
export M2SETUP_DB_HOST=127.0.0.1
export M2SETUP_DB_USER=root
export M2SETUP_DB_PASSWORD=magento
export M2SETUP_DB_NAME=magento
#
# Create temporary branch for installation in Magento
#
git checkout -b tmp
git add -A
git commit --allow-empty -m "tmp"
#
# Install Magento
#
curl -L http://pubfiles.nexcess.net/magento/ce-packages/magento2-$M2SETUP_VERSION.tar.gz | tar xzf - -o -C /var/www/html/
cd /var/www/html
/usr/local/bin/php ./bin/magento setup:install \
  --db-host=${M2SETUP_DB_HOST} \
  --db-name=${M2SETUP_DB_NAME} \
  --db-user=${M2SETUP_DB_USER} \
  --db-password=${M2SETUP_DB_PASSWORD} \
  --base-url=http://127.0.0.1:8082/index.php/ \
  --admin-firstname=Admin \
  --admin-lastname=User \
  --admin-email=dummy@example.com \
  --admin-user=magento2 \
  --admin-password=magento2 \
  --language=en_US \
  --currency=USD \
  --timezone=America/New_York
/usr/local/bin/php ./bin/magento deploy:mode:set developer
#
# Install module from temporary branch
#
composer config http-basic.repo.magento.com ${MAGENTO_REPO_PUBLIC_KEY} ${MAGENTO_REPO_PRIVATE_KEY}
composer config repositories.module vcs ${MODULE_DIR}
composer require deity-core/deity-magento-api dev-tmp@dev
/usr/local/bin/php ./bin/magento module:enable Deity_MagentoApi
/usr/local/bin/php ./bin/magento setup:upgrade
#
# Copy test configuration
#
cp ${MODULE_DIR}/tests/integration/phpunit.xml.dist /var/www/html/dev/tests/integration/phpunit.xml
cp ${MODULE_DIR}/tests/integration/install-config-mysql.php /var/www/html/dev/tests/integration/etc/
cp ${MODULE_DIR}/tests/api-functional/phpunit.xml.dist /var/www/html/dev/tests/api-functional/phpunit.xml
cp ${MODULE_DIR}/tests/api-functional/install-config-mysql.php /var/www/html/dev/tests/api-functional/config/
sed -i -e "s/DB_HOST/$M2SETUP_DB_HOST/g" /var/www/html/dev/tests/integration/etc/install-config-mysql.php
sed -i -e "s/DB_USER/$M2SETUP_DB_USER/g" /var/www/html/dev/tests/integration/etc/install-config-mysql.php
sed -i -e "s/DB_PASSWORD/$M2SETUP_DB_PASSWORD/g" /var/www/html/dev/tests/integration/etc/install-config-mysql.php
sed -i -e "s/DB_NAME/$M2SETUP_DB_NAME/g" /var/www/html/dev/tests/integration/etc/install-config-mysql.php
sed -i -e "s/DB_HOST/$M2SETUP_DB_HOST/g" /var/www/html/dev/tests/api-functional/config/install-config-mysql.php
sed -i -e "s/DB_USER/$M2SETUP_DB_USER/g" /var/www/html/dev/tests/api-functional/config/install-config-mysql.php
sed -i -e "s/DB_PASSWORD/$M2SETUP_DB_PASSWORD/g" /var/www/html/dev/tests/api-functional/config/install-config-mysql.php
sed -i -e "s/DB_NAME/$M2SETUP_DB_NAME/g" /var/www/html/dev/tests/api-functional/config/install-config-mysql.php
#
# Run built in webserver (sufficient for API tests, and nginx service does not work well with Bitbucket Pipelines
# because it is not possible to share volumes)
#
/usr/local/bin/php -S 127.0.0.1:8082 -t /var/www/html/pub/ /var/www/html/phpserver/router.php &
#
# Run integration tests
#
# Real path to phpunit executable must be used (not symlink in vendor/bin), otherwise composer autoloader is not found
#
cd /var/www/html/dev/tests/integration
/usr/local/bin/php ../../../vendor/phpunit/phpunit/phpunit --log-junit ${MODULE_DIR}/test-reports/integration.xml
#
# Curl smoke test (check if built in webserver can be reached and REST API call works)
#
curl -sSv http://127.0.0.1:8082/index.php/rest/V1/info
echo
#
# Run api functional tests
#
# Real path to phpunit executable must be used (not symlink in vendor/bin), otherwise composer autoloader is not found
#
cd /var/www/html/dev/tests/api-functional
/usr/local/bin/php ../../../vendor/phpunit/phpunit/phpunit --log-junit ${MODULE_DIR}/test-reports/api-functional.xml
#
# Production mode: test if compilation runs without errors
cd /var/www/html
/usr/local/bin/php ./bin/magento deploy:mode:set production
