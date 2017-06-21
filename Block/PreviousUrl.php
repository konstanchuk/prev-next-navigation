<?php

/**
 * Previous Next Navigation Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\PrevNextNavigation\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Konstanchuk\PrevNextNavigation\Helper\Data as Helper;


class PreviousUrl extends Template
{
    /** @var Registry  */
    protected $_registry;

    /** @var Helper  */
    protected $_helper;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        Helper $helper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->_helper = $helper;
    }

    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    public function toHtml()
    {
        $category = $this->getCurrentCategory();
        if ($this->_helper->isEnabled() && $category && $category->getId()) {
            return parent::toHtml();
        }
    }
}