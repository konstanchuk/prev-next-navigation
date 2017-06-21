<?php

/**
 * Previous Next Navigation Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\PrevNextNavigation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;


class Data extends AbstractHelper
{
    const XML_PATH_IS_ENABLED = 'catalog/konstanchuk_pn_navigation/active';
    const XML_PATH_NAME_IN_LINK = 'catalog/konstanchuk_pn_navigation/name_in_link';
    const XML_PATH_SHOW_IMAGE = 'catalog/konstanchuk_pn_navigation/show_image';
    const XML_PATH_BACK_ON_CATEGORY = 'catalog/konstanchuk_pn_navigation/back_on_category';

    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            static::XML_PATH_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function showProductNameInLink()
    {
        return (bool)$this->scopeConfig->getValue(
            static::XML_PATH_NAME_IN_LINK,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function showImageInLink()
    {
        return (bool)$this->scopeConfig->getValue(
            static::XML_PATH_SHOW_IMAGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function showBackOnCategoryBtn()
    {
        return (bool)$this->scopeConfig->getValue(
            static::XML_PATH_BACK_ON_CATEGORY,
            ScopeInterface::SCOPE_STORE
        );
    }
}