/**
 * Previous Next Navigation Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
define(
    [
        'konstanchuk/prevNextNavigationStorage'
    ],
    function (storage) {
        'use strict';

        return function (config, element) {
            try {
                if (storage.isSupported()) {
                    if (document.referrer) {
                        storage.removeValue(storage.parseUrl(document.referrer).pathname);
                    }
                    storage.setValue(window.location.pathname, config.current_category_id);
                }
            } catch (e) {
                console.log($.mage.__('PREVIOUS NEXT NAVIGATION ERROR: %1').replace('%1', e.message));
            }
        };
    }
);