/**
 * Previous Next Navigation Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
define(['underscore'], function (_) {
        'use strict';

        var STORAGE_KEY = 'prev_next_storage';
        var ACTIVE_HOURS = 3;

        function _getValues() {
            if (isSupported()) {
                var storage = window.localStorage.getItem(STORAGE_KEY);
                return storage ? JSON.parse(storage) : {};
            } else {
                return {};
            }
        }

        function _setValues(values) {
            if (isSupported()) {
                window.localStorage.setItem(STORAGE_KEY, JSON.stringify(values));
            }
        }

        function isSupported() {
            return window.localStorage;
        }

        function setValue(urlPath, value) {
            var storage = _getValues();
            storage[urlPath] = {
                v: value,
                t: Date.now()
            };
            _setValues(storage);
        }

        function getValue(urlPath) {
            try {
                var storage = _getValues();
                if (_.has(storage, urlPath)) {
                    return storage[urlPath].v;
                }
            } catch (e) {
                removeStorage();
            }
            return null;
        }

        function removeValue(urlPath) {
                var storage = _getValues();
                if (_.has(storage, urlPath)) {
                    delete storage[urlPath];
                    _setValues(storage);
                }
                return true;
        }

        function removeStorage() {
            if (isSupported()) {
                window.localStorage.removeItem(STORAGE_KEY);
            }
        }

        function parseUrl(url) {
            var l = document.createElement('a');
            l.href = url;
            return l;
        }

        function filterOldValues() {
            try {
                var currentTime = Date.now(), values = _getValues();
                if (typeof values !== 'object') {
                    removeStorage();
                    return;
                }
                for (var index in values) {
                    if (!values.hasOwnProperty(index)) {
                        continue;
                    }
                    // different hours between time
                    if (Math.abs(currentTime - values[index].t) / 36e5 > ACTIVE_HOURS) {
                        delete values[index];
                    }
                }
                _setValues(values);
            } catch (e) {
                removeStorage();
            }
        }

        /* remove oldValues from storage */
        filterOldValues();

        return {
            removeStorage: removeStorage,
            removeValue: removeValue,
            getValue: getValue,
            setValue: setValue,
            isSupported: isSupported,
            parseUrl: parseUrl
        };
    }
);