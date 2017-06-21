<?php

/**
 * Previous Next Navigation Extension for Magento 2
 *
 * @author     Volodymyr Konstanchuk http://konstanchuk.com
 * @copyright  Copyright (c) 2017 The authors
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace Konstanchuk\PrevNextNavigation\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Helper\Product as ProductHelper;
use Konstanchuk\PrevNextNavigation\Helper\Data as Helper;


class PrevNext
{
    /** @var CategoryRepositoryInterface */
    protected $_categoryRepository;

    /** @var  ProductRepositoryInterface */
    protected $_productRepository;

    /** @var  CatalogConfig */
    protected $_catalogConfig;

    /** @var ProductHelper */
    protected $_productHelper;

    /** @var Helper */
    protected $_helper;

    protected $cache = [];

    protected $categoryProductIds = [];

    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository,
        CatalogConfig $catalogConfig,
        ProductHelper $productHelper,
        Helper $helper
    )
    {
        $this->_categoryRepository = $categoryRepository;
        $this->_productRepository = $productRepository;
        $this->_catalogConfig = $catalogConfig;
        $this->_helper = $helper;
        $this->_productHelper = $productHelper;
    }

    protected function prepareCache($categoryId, $productId)
    {
        $keyCache = $categoryId . ':' . $productId;
        if (isset($this->cache[$keyCache])) {
            return $this->cache[$keyCache];
        }
        $prevProductId = null;
        $nextProductId = null;
        $category = null;
        try {
            if (isset($this->categoryProductIds[$categoryId])) {
                $productIds = $this->categoryProductIds[$categoryId];
            } else {
                /** @var \Magento\Catalog\Model\Category $category */
                $category = $this->_categoryRepository->get($categoryId);
                $order = $this->_catalogConfig->getProductListDefaultSortBy();
                $productIds = $category->getProductCollection()
                    ->addAttributeToSort($order, \Magento\Catalog\Helper\Product\ProductList::DEFAULT_SORT_DIRECTION)
                    ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                    ->addStoreFilter()
                    ->addAttributeToFilter('is_saleable', 1)
                    ->getAllIds();
                $this->categoryProductIds[$categoryId] = $productIds;
            }
            $pos = array_search($productId, $productIds);
            if ($pos !== false) {
                $prevProductId = $productIds[$pos - 1] ?? null;
                $nextProductId = $productIds[$pos + 1] ?? null;
            }
        } catch (\Exception $e) {
        }
        return $this->cache[$keyCache] = [
            'prev' => $prevProductId,
            'next' => $nextProductId,
            'category' => $category,
        ];
    }

    public function getPrevProduct($productId, $categoryId)
    {
        $cache = $this->prepareCache($categoryId, $productId);
        try {
            if (isset($cache['prev'])) {
                return $this->_productRepository->getById($cache['prev']);
            }
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    public function getNextProduct($productId, $categoryId)
    {
        $cache = $this->prepareCache($categoryId, $productId);
        try {
            if (isset($cache['next'])) {
                return $this->_productRepository->getById($cache['next']);
            }
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    public function getCategory($productId, $categoryId)
    {
        $cache = $this->prepareCache($categoryId, $productId);
        return $cache['category'] ?? null;
    }

    public function getPrevNextArray($productId, $categoryId)
    {
        /** @var \Magento\Catalog\Model\Product $prevProduct */
        $prevProduct = $this->getPrevProduct($productId, $categoryId);
        /** @var \Magento\Catalog\Model\Product $nextProduct */
        $nextProduct = $this->getNextProduct($productId, $categoryId);

        if ($prevProduct) {
            $prevData = [
                'id' => $prevProduct->getId(),
                'url' => $prevProduct->getUrlModel()->getUrl($prevProduct),
                'name' => $prevProduct->getName(),
            ];
            if ($this->_helper->showImageInLink()) {
                $prevData['img'] = $this->_productHelper->getSmallImageUrl($prevProduct);
            }
        } else {
            $prevData = null;
        }

        if ($nextProduct) {
            $nextData = [
                'id' => $nextProduct->getId(),
                'url' => $nextProduct->getUrlModel()->getUrl($nextProduct),
                'name' => $nextProduct->getName(),
            ];
            if ($this->_helper->showImageInLink()) {
                $nextData['img'] = $this->_productHelper->getSmallImageUrl($nextProduct);
            }
        } else {
            $nextData = null;
        }

        $data = [
            'prev_product' => $prevData,
            'next_product' => $nextData,
        ];

        if ($this->_helper->showBackOnCategoryBtn()) {
            $category = $this->getCategory($productId, $categoryId);
            if ($category && $category->getId()) {
                $data['category_url'] = $category->getUrl();
            }
        }

        return $data;
    }
}