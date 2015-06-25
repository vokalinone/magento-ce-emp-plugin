Genesis client for Magento CE
=============================

This is a Payment Module for Magento Community Edition, that gives you the ability to process payments through eMerchantPay's Payment Gateway - Genesis.

Requirements
------------

* Magento Community Edition* > 1.7
* GenesisPHP 1.2
* PCI-certified server in order to use ```eMerchantPay Direct```

*Note: this module has been tested only with Magento __Community Edition__, it may not work
as intended with Magento __Enterprise Edition__

GenesisPHP Requirements
------------

* PHP version 5.3.2 or newer
* PHP Extensions:
    * [BCMath](https://php.net/bcmath)
    * [CURL](https://php.net/curl) (required, only if you use the curl network interface)
    * [Filter](https://php.net/filter)
    * [Hash](https://php.net/hash)
    * [XMLReader](https://php.net/xmlreader)
    * [XMLWriter](https://php.net/xmlwriter)

Installation (via Modman)
---------------------

* Install [ModMan]
* Navigate to the root of your Magento installation
* run ```modman init```
* and clone this repo ```modman clone https://github.com/E-ComProcessing/genesis_php```
* Login inside the Admin Panel and go to ```System``` -> ```Configuration``` -> ```Payment Methods```
* Check ```Enable```, set the correct credentials, select your prefered payment method and click ```Save config```

Installation (manual)
---------------------

* Copy the files to the root folder of your Magento installation
* Login inside the Admin Panel and go to ```System``` -> ```Configuration``` -> ```Payment Methods````
* Check ```Enable```, set the correct credentials, select your prefered payment method and click ```Save config```

You're now ready to process payments through our gateway.

[ModMan]: https://github.com/colinmollenhour/modman
