# MageWorx SEO Category Grid Extension for Magento 2

## Upload the extension

### Upload via Composer

1. Log into Magento server (or switch to) as a user who has permissions to write to the Magento file system.
2. Create a folder anywhere on your server (preferably not in the Magento install dir). When done, upload all extension zip packages in there.
3. To use the folder created above as a packaging repository, add the following piece of code to the composer.json file:
```
    {
        "repositories": [
            {
                "type": "artifact",
                "url": "path/to/directory/with/extension/zips/"
            }
        ]
    }
```

4. Install the extension with Composer:
```
composer require mageworx/module-seocategorygrid
```

### Upload by copying code

1. Log into Magento server (or switch to) as a user who has permissions to write to the Magento file system.
2. Download the "Ready to paste" package from your customer's area, unzip it and upload the 'app' folder to your Magento install dir.


## Enable the extension

1. Log in to the Magento server as, or switch to, a user who has permissions to write to the Magento file system.
2. Go to your Magento install dir:
```
cd <your Magento install dir>
```

3. Enable the extension:
```
php bin/magento module:enable MageWorx_SeoCategoryGrid
```

4. And finally, update the database:
```
php bin/magento setup:upgrade
php bin/magento cache:flush
php bin/magento setup:static-content:deploy
```