<?php

/**
 * Previous Next Navigation Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\PrevNextNavigation\Controller\Get;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Konstanchuk\PrevNextNavigation\Model\PrevNext as PrevNextModel;
use Konstanchuk\PrevNextNavigation\Helper\Data as Helper;


class Index extends Action
{
    /** @var PrevNextModel  */
    protected $_prevNextModel;

    /** @var Helper  */
    protected $_helper;

    /** @var JsonFactory  */
    protected $_jsonFactory;

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        PrevNextModel $prevNextModel,
        Helper $helper
    )
    {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_prevNextModel = $prevNextModel;
        $this->_jsonFactory = $jsonFactory;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('product_id');
        $categoryId = $this->getRequest()->getParam('category_id');
        try {
            if ($productId && $categoryId) {
                $data = $this->_prevNextModel->getPrevNextArray($productId, $categoryId);
            } else {
                $data = [];
            }
        } catch (\Exception $e) {
            $data = [];
        }
        $result = $this->_jsonFactory->create();
        $result->setData($data);
        return $result;
    }
}