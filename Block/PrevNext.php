<?php

/**
 * Previous Next Navigation Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\PrevNextNavigation\Block;

use Magento\Catalog\Block\Product\View\AbstractView;
use Konstanchuk\PrevNextNavigation\Helper\Data as Helper;
use Konstanchuk\PrevNextNavigation\Model\PrevNext as PrevNextModel;


class PrevNext extends AbstractView
{
    /** @var Helper  */
    protected $_helper;

    /** @var PrevNextModel  */
    protected $_prevNextModel;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        Helper $helper,
        PrevNextModel $prevNextModel,
        array $data = []
    )
    {
        parent::__construct($context, $arrayUtils, $data);
        $this->_helper = $helper;
        $this->_prevNextModel = $prevNextModel;
    }

    public function getHelper()
    {
        return $this->_helper;
    }

    public function getDefaultCategoryId()
    {
        $categoriesIds = $this->getProduct()->getCategoryIds();
        if (is_array($categoriesIds) && isset($categoriesIds[0])) {
            return $categoriesIds[0];
        }
        return null;
    }

    public function getDefaultValues()
    {
        try {
            $defaultCategoryId = $this->getDefaultCategoryId();
            if ($defaultCategoryId) {
                return $this->_prevNextModel->getPrevNextArray($this->getProduct()->getId(), $defaultCategoryId);
            }
        } catch (\Exception $e) {}
        return [];
    }
}