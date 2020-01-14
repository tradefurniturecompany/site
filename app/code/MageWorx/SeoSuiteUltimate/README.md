# MageWorx Seo Suite Ultimate Extension for Magento 2

## Upload the extension

### Upload via Composer

1. Log into Magento server (or switch to) as a user who has permissions to write to the Magento file system.
2. Create a folder anywhere on your server (preferably not in the Magento install dir). When done, upload all extension zip packages in there.
3. To use the folder created above as a packaging repository, add the run composer command:
```
    composer config repositories.mageworx artifact {YOUR/ABSOLUTE/PATH/TO/EXTENSIONS/DIRECTORY}
```
For example:
```
    composer config repositories.mageworx artifact /Users/mageworxuser/magento_extensions/mageworx/zip
```

This command add to your composer.json file this lines:

```
    ,
    "mageworx": {
        "type": "artifact",
        "url": "/Users/mageworxuser/magento_extensions/mageworx/zip"
    }
```

4. Install the extension with Composer:
```
composer require mageworx/module-seosuiteultimate
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

Enable the module

Log into the Magento server (or switch to) as a user, who has permissions to write to the Magento file system.
Go to your Magento install dir:  cd <your Magento install dir>   

3. Enable the module: 
```
php bin/magento module:enable MageWorx_SeoAll MageWorx_SeoBase MageWorx_SeoXTemplates \
MageWorx_SeoCrossLinks MageWorx_XmlSitemap MageWorx_HtmlSitemap MageWorx_SeoRedirects \
MageWorx_SeoMarkup MageWorx_SeoExtended MageWorx_SeoBreadcrumbs MageWorx_SeoUrls \
MageWorx_SeoCategoryGrid
```

4. And finally, update the database:
```
php bin/magento setup:upgrade
php bin/magento cache:flush
php bin/magento setup:static-content:deploy
```