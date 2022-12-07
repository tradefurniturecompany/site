[tradefurniturecompany.co.uk](https://www.tradefurniturecompany.co.uk) (Magento 2).

## How to update all `tradefurniturecompany/*` packages 
```                 
sudo service crond stop
sudo service nginx stop                
sudo service php-fpm stop
bin/magento maintenance:enable      
composer remove tradefurniturecompany/core
composer remove tradefurniturecompany/blog
composer remove tradefurniturecompany/google-shopping
composer remove tradefurniturecompany/image 
composer remove tradefurniturecompany/report  
rm -rf composer.lock
composer clear-cache
composer2 require --ignore-platform-reqs --no-plugins tradefurniturecompany/core:*
composer2 require --ignore-platform-reqs --no-plugins tradefurniturecompany/blog:*
composer2 require --ignore-platform-reqs --no-plugins tradefurniturecompany/google-shopping:*
composer2 require --ignore-platform-reqs --no-plugins tradefurniturecompany/image:*
composer2 require --ignore-platform-reqs --no-plugins tradefurniturecompany/report:*
composer update # https://mage2.pro/t/6327/2 
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/*
bin/magento setup:di:compile
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US en_GB
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme TradeFurnitureCompany/default \
	-f en_GB
bin/magento cache:clean
sudo service php-fpm start
sudo service nginx start
bin/magento maintenance:disable
sudo service crond start
rm -rf var/log/* var/report/*
```