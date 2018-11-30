#OXID6

##Module installation via composer

In order to install the module via composer, firstly you need to extract the contents of the archive into the root directory of your shop, example directory name Artifacts. Run following commands in commandline in your shop base directory (where the shop's composer.json file resides).

```
composer config repositories.yc/oxid artifact ./Artifacts/
composer require yoochoose/oxid
```

During the composer command you will be asked:.

Update operation will overwrite yoochoose/oxid files. Do you want to continue? (y/N)

Please, type: Y
