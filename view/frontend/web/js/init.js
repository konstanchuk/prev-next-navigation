/**
 * Previous Next Navigation Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
define(
    [
        'jquery',
        'uiComponent',
        'ko',
        'underscore',
        'konstanchuk/prevNextNavigationStorage'
    ],
    function ($, Component, ko, _, storage) {
        'use strict';
        return Component.extend({
            defaults: {
                get_url: '/prev-next/get'
            },
            prevProduct: ko.observable(false),
            nextProduct: ko.observable(false),
            categoryUrl: ko.observable(false),
            visible: ko.observable(false),
            initialize: function () {
                var self = this;
                self._super();
                var categoryId = null, showCategory = false, pathname, currentPathname = window.location.pathname;
                try {
                    if (storage.isSupported()) {
                        if (document.referrer) {
                            pathname = storage.parseUrl(document.referrer).pathname;
                            categoryId = storage.getValue(pathname);
                        }

                        if (categoryId) {
                            storage.removeValue(pathname);
                        } else {
                            categoryId = storage.getValue(currentPathname);
                        }
                        if (categoryId) {
                            showCategory = true;
                        }
                    }

                    if (self.category_ids.length && (!categoryId || !_.contains(self.category_ids, categoryId))) {
                        categoryId = self.default_category_id;
                        showCategory = false;
                    }

                    if (categoryId) {
                        self._loadNavigation(categoryId, function (data) {
                            var visible = false;
                            if (data.hasOwnProperty('prev_product')) {
                                self.prevProduct(data.prev_product);
                                visible = true;
                            } else {
                                self.prevProduct(false);
                            }
                            if (data.hasOwnProperty('next_product')) {
                                self.nextProduct(data.next_product);
                                visible = true;
                            } else {
                                self.nextProduct(false);
                            }
                            if (showCategory && data.hasOwnProperty('category_url')) {
                                self.categoryUrl(data.category_url);
                            } else {
                                self.categoryUrl(false);
                            }
                            self.visible(visible);
                            $('.pn-btns').show();
                        });
                        if (storage.isSupported() && showCategory) {
                            storage.setValue(currentPathname, categoryId);
                        }
                    }
                } catch (e) {
                    storage.removeStorage();
                    console.log($.mage.__('PREVIOUS NEXT NAVIGATION ERROR: %1').replace('%1', e.message));
                }
            },
            _loadNavigation: function (categoryId, callback) {
                if (this.default_category_id == categoryId) {
                    callback(this.default_values);
                } else {
                    $.ajax({
                        url: this.get_url,
                        cache: true,
                        data: {
                            category_id: categoryId,
                            product_id: this.current_product_id
                        }
                    }).done(callback);
                }
            }
        });
    }
);