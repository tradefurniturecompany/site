<?php
/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\SeoBase\Ui\DataProvider\Product\Form\Modifier;

use MageWorx\SeoBase\Api\Data\CustomCanonicalInterface;
use MageWorx\SeoBase\Api\CustomCanonicalRepositoryInterface;
use MageWorx\SeoBase\Model\Source\CustomCanonical\TargetStoreId;
use MageWorx\SeoBase\Model\Source\CustomCanonical\TargetTypeEntity;
use MageWorx\SeoBase\Model\Source\CustomCanonical\CanonicalUrlType;
use MageWorx\SeoBase\Model\Source\CustomCanonical\CmsPage as CmsPageSource;
use Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Modal;
use Magento\Catalog\Model\Locator\LocatorInterface;
use MageWorx\SeoBase\Serializer\SerializeJson;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\DB\Helper as DbHelper;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Cms\Model\Page as CmsPageModel;

class CustomCanonical extends AbstractModifier
{
    const TARGET_PRODUCT_ID_FIELD  = Rewrite::ENTITY_TYPE_PRODUCT . '_' . CustomCanonicalInterface::TARGET_ENTITY_ID;
    const TARGET_CATEGORY_ID_FIELD = Rewrite::ENTITY_TYPE_CATEGORY . '_' . CustomCanonicalInterface::TARGET_ENTITY_ID;
    const TARGET_CMS_PAGE_ID_FIELD = Rewrite::ENTITY_TYPE_CMS_PAGE . '_' . CustomCanonicalInterface::TARGET_ENTITY_ID;

    const TARGET_PRODUCT_CONTAINER  = 'target_product_container';
    const TARGET_PRODUCT_LABEL      = 'target_product_label';
    const TARGET_PRODUCT_BUTTON     = 'target_product_button';
    const TARGET_PRODUCT_GRID_MODAL = 'choose_target_product_modal';

    const TARGET_CATEGORY_TREE_CACHE_ID    = 'CANONICAL_TO_CATEGORY_TREE';
    const TARGET_CMS_PAGE_OPTIONS_CACHE_ID = 'CANONICAL_TO_CMS_PAGE_OPTIONS';

    const DATA_SCOPE = 'mageworx_seobase_canonical_to';

    /**
     * @var string
     */
    private static $currentGroup = 'search-engine-optimization';

    /**
     * @var TargetStoreId
     */
    private $targetStoreId;

    /**
     * @var CustomCanonicalInterface|null
     */
    private $customCanonical;

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var SerializeJson
     */
    private $serializer;

    /**
     * @var CacheInterface
     */
    private $cacheManager;

    /**
     * @var DbHelper
     */
    private $dbHelper;

    /**
     * @var ResourceProduct
     */
    private $resourceProduct;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var CmsPageSource
     */
    private $cmsPageSource;

    /**
     * @var TargetTypeEntity
     */
    private $targetTypeEntity;

    /**
     * @var CanonicalUrlType
     */
    private $canonicalUrlType;

    /**
     * CustomCanonical constructor.
     *
     * @param LocatorInterface $locator
     * @param SerializeJson $serializer
     * @param CacheInterface $cacheManager
     * @param DbHelper $dbHelper
     * @param ResourceProduct $resourceProduct
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CmsPageSource $cmsPageSource
     * @param TargetStoreId $targetStoreId
     * @param TargetTypeEntity $targetTypeEntity
     * @param CanonicalUrlType $canonicalUrlType
     * @param CustomCanonicalRepositoryInterface $customCanonicalRepository
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        LocatorInterface $locator,
        SerializeJson $serializer,
        CacheInterface $cacheManager,
        DbHelper $dbHelper,
        ResourceProduct $resourceProduct,
        CategoryCollectionFactory $categoryCollectionFactory,
        CmsPageSource $cmsPageSource,
        TargetStoreId $targetStoreId,
        TargetTypeEntity $targetTypeEntity,
        CanonicalUrlType $canonicalUrlType,
        CustomCanonicalRepositoryInterface $customCanonicalRepository
    ) {
        $this->locator                   = $locator;
        $this->serializer                = $serializer;
        $this->cacheManager              = $cacheManager;
        $this->dbHelper                  = $dbHelper;
        $this->resourceProduct           = $resourceProduct;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->cmsPageSource             = $cmsPageSource;
        $this->targetStoreId             = $targetStoreId;
        $this->targetTypeEntity          = $targetTypeEntity;
        $this->canonicalUrlType          = $canonicalUrlType;

        $this->customCanonical = $customCanonicalRepository->getBySourceEntityData(
            Rewrite::ENTITY_TYPE_PRODUCT,
            $this->locator->getProduct()->getId(),
            $this->locator->getStore()->getId()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (isset($meta[self::$currentGroup]['children'])) {
            $meta[self::$currentGroup]['children']['canonical_url_type']  = $this->getCanonicalUrlTypeConfig(200);
            $meta[self::$currentGroup]['children']['custom_canonical_id'] = $this->getCustomCanonicalIdConfig(201);
            $meta[self::$currentGroup]['children'][self::DATA_SCOPE]      = $this->getCanonicalToFieldset(202);
        }

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    private function getCanonicalUrlTypeConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'label'         => __('Canonical Url'),
                        'formElement'   => Select::NAME,
                        'options'       => $this->canonicalUrlType->toOptionArray(),
                        'sortOrder'     => $sortOrder,
                        'value'         => $this->customCanonical ? 1 : 0
                    ]
                ]
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    private function getCustomCanonicalIdConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement'   => Hidden::NAME,
                        'elementTmpl'   => 'ui/form/element/hidden',
                        'sortOrder'     => $sortOrder,
                        'value'         => $this->customCanonical ? $this->customCanonical->getId() : null
                    ]
                ]
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    private function getCanonicalToFieldset($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataScope'     => self::DATA_SCOPE,
                        'componentType' => Fieldset::NAME,
                        'label'         => __('Canonical To:'),
                        'sortOrder'     => $sortOrder,
                        'collapsible'   => true,
                        'imports'       => [
                            'visible' => '${ $.provider }:data.product.canonical_url_type'
                        ]
                    ]
                ]
            ],
            'children'  => [
                CustomCanonicalInterface::TARGET_STORE_ID    => $this->getTargetStoreIdConfig(10),
                CustomCanonicalInterface::TARGET_ENTITY_TYPE => $this->getTargetEntityTypeConfig(20),
                CustomCanonicalInterface::TARGET_ENTITY_ID   => $this->getTargetUrlConfig(30),
                self::TARGET_PRODUCT_CONTAINER               => $this->getTargetProductContainerConfig(40),
                self::TARGET_CATEGORY_ID_FIELD               => $this->getTargetCategoryIdFieldConfig(50),
                self::TARGET_CMS_PAGE_ID_FIELD               => $this->getTargetCmsPageIdFieldConfig(60),
                self::TARGET_PRODUCT_GRID_MODAL              => $this->getTargetProductGridModalConfig()
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    private function getTargetStoreIdConfig($sortOrder)
    {
        if ($this->customCanonical) {
            $value = $this->customCanonical->getTargetStoreId();
        } else {
            $value = TargetStoreId::SAME_AS_SOURCE_ENTITY;
        }

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'label'         => __('Store View'),
                        'formElement'   => Select::NAME,
                        'options'       => $this->targetStoreId->toOptionArray(),
                        'sortOrder'     => $sortOrder,
                        'value'         => $value,
                        'validation'    => [
                            'required-entry' => true
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    private function getTargetEntityTypeConfig($sortOrder)
    {
        $value = $this->customCanonical ? $this->customCanonical->getTargetEntityType() : Rewrite::ENTITY_TYPE_CUSTOM;

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'component'     => 'MageWorx_SeoBase/js/form/element/target-entity-type',
                        'label'         => __('Type'),
                        'formElement'   => Select::NAME,
                        'options'       => $this->targetTypeEntity->toOptionArray(),
                        'value'         => $value,
                        'sortOrder'     => $sortOrder,
                        'validation'    => [
                            'required-entry' => true
                        ],
                        'indexies'      => [
                            'target_url'      => CustomCanonicalInterface::TARGET_ENTITY_ID,
                            'target_product'  => self::TARGET_PRODUCT_CONTAINER,
                            'target_category' => self::TARGET_CATEGORY_ID_FIELD,
                            'target_cms_page' => self::TARGET_CMS_PAGE_ID_FIELD
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    private function getTargetUrlConfig($sortOrder)
    {
        if (!$this->customCanonical) {
            $value   = null;
            $visible = true;
        } elseif ($this->customCanonical
            && $this->customCanonical->getTargetEntityType() == Rewrite::ENTITY_TYPE_CUSTOM
        ) {
            $value   = $this->customCanonical->getTargetEntityId();
            $visible = true;
        } else {
            $value   = null;
            $visible = false;
        }

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'label'         => __('URL'),
                        'formElement'   => Input::NAME,
                        'sortOrder'     => $sortOrder,
                        'value'         => $value,
                        'visible'       => $visible,
                        'visibleValue'  => Rewrite::ENTITY_TYPE_CUSTOM,
                        'notice'        => __(
                            "Link without 'http[s]://' as 'my/custom/url'
                            will be converted to 'http[s]://(store_URL_here)/my/custom/url'.
                            Link with 'http[s]://' will be added as it is."
                        ),
                        'validation'    => [
                            'required-entry' => true
                        ],
                        'imports'       => [
                            // cancel validation if "Canonical Url - Use Default"
                            'disabled' => '!${ $.provider }:data.product.canonical_url_type'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    private function getTargetCategoryIdFieldConfig($sortOrder)
    {
        if ($this->customCanonical && $this->customCanonical->getTargetEntityType() == Rewrite::ENTITY_TYPE_CATEGORY) {
            $value   = $this->customCanonical->getTargetEntityId();
            $visible = true;
        } else {
            $value   = null;
            $visible = false;
        }

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement'      => Select::NAME,
                        'componentType'    => Field::NAME,
                        'label'            => __('Category'),
                        'component'        => 'Magento_Ui/js/form/element/ui-select',
                        'filterOptions'    => true,
                        'multiple'         => false,
                        'disableLabel'     => true,
                        'levelsVisibility' => '1',
                        'elementTmpl'      => 'ui/grid/filters/elements/ui-select',
                        'options'          => $this->getCategoryTree(),
                        'sortOrder'        => $sortOrder,
                        'value'            => $value,
                        'visible'          => $visible,
                        'visibleValue'     => Rewrite::ENTITY_TYPE_CATEGORY,
                        'validation'       => [
                            'required-entry' => true
                        ]
                    ],
                ],
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    private function getTargetCmsPageIdFieldConfig($sortOrder)
    {
        if ($this->customCanonical && $this->customCanonical->getTargetEntityType() == Rewrite::ENTITY_TYPE_CMS_PAGE) {
            $value   = $this->customCanonical->getTargetEntityId();
            $visible = true;
        } else {
            $value   = null;
            $visible = false;
        }

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement'   => Select::NAME,
                        'componentType' => Field::NAME,
                        'label'         => __('CMS Page'),
                        'component'     => 'Magento_Ui/js/form/element/ui-select',
                        'filterOptions' => true,
                        'multiple'      => false,
                        'disableLabel'  => true,
                        'elementTmpl'   => 'ui/grid/filters/elements/ui-select',
                        'options'       => $this->getCmsPageOptions(),
                        'sortOrder'     => $sortOrder,
                        'value'         => $value,
                        'visible'       => $visible,
                        'visibleValue'  => Rewrite::ENTITY_TYPE_CMS_PAGE,
                        'validation'    => [
                            'required-entry' => true
                        ]
                    ],
                ],
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    private function getTargetProductContainerConfig($sortOrder)
    {
        if ($this->customCanonical && $this->customCanonical->getTargetEntityType() == Rewrite::ENTITY_TYPE_PRODUCT) {
            $productId   = $this->customCanonical->getTargetEntityId();
            $productName = $this->resourceProduct->getAttributeRawValue(
                $productId,
                'name',
                $this->locator->getStore()->getId()
            );
            $visible     = true;
        } else {
            $productId   = null;
            $productName = 'Not Selected';
            $visible     = false;
        }

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'dataScope'     => '',
                        'breakLine'     => false,
                        'formElement'   => Container::NAME,
                        'componentType' => Container::NAME,
                        'component'     => 'MageWorx_SeoBase/js/form/components/group',
                        'visible'       => $visible,
                        'visibleValue'  => Rewrite::ENTITY_TYPE_PRODUCT,
                        'sortOrder'     => $sortOrder,
                        'indexies'      => [
                            'product_id'         => self::TARGET_PRODUCT_ID_FIELD,
                            'product_label'      => self::TARGET_PRODUCT_LABEL,
                            'product_grid_modal' => self::TARGET_PRODUCT_GRID_MODAL
                        ]
                    ],
                ],
            ],
            'children'  => [
                self::TARGET_PRODUCT_LABEL    => $this->getTargetProductLabelConfig($productName, 10),
                self::TARGET_PRODUCT_ID_FIELD => $this->getTargetProductIdFieldConfig($productId, 20),
                self::TARGET_PRODUCT_BUTTON   => $this->getTargetProductButtonConfig(30)
            ]
        ];
    }

    /**
     * @param string|null $value
     * @param int $sortOrder
     * @return array
     */
    private function getTargetProductIdFieldConfig($value, $sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement'   => Hidden::NAME,
                        'elementTmpl'   => 'ui/form/element/hidden',
                        'sortOrder'     => $sortOrder,
                        'value'         => $value,
                        'validation'    => [
                            'required-entry' => true
                        ],
                        'imports'       => [
                            // cancel validation if target_product_container is not visible
                            'visible' => 'ns = ${ $.ns }, index = ' . self::TARGET_PRODUCT_CONTAINER . ':visible'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param string $value
     * @param int $sortOrder
     * @return array
     */
    private function getTargetProductLabelConfig($value, $sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'             => __('Product'),
                        'additionalClasses' => 'required',
                        'componentType'     => Field::NAME,
                        'formElement'       => Input::NAME,
                        'elementTmpl'       => 'MageWorx_SeoBase/form/element/text',
                        'sortOrder'         => $sortOrder,
                        'value'             => $value,
                    ]
                ]
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    private function getTargetProductButtonConfig($sortOrder)
    {
        $modalTarget = 'product_form.product_form.' . self::$currentGroup . '.'
            . self::DATA_SCOPE . '.' . self::TARGET_PRODUCT_GRID_MODAL;

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'title'              => __('Choose...'),
                        'formElement'        => Container::NAME,
                        'additionalClasses'  => 'admin__field-small',
                        'componentType'      => Container::NAME,
                        'component'          => 'Magento_Ui/js/form/components/button',
                        'template'           => 'ui/form/components/button/container',
                        'actions'            => [
                            [
                                'targetName' => $modalTarget,
                                'actionName' => 'toggleModal',
                            ],
                            [
                                'targetName' => $modalTarget . '.product_listing',
                                'actionName' => 'render'
                            ]
                        ],
                        'additionalForGroup' => true,
                        'displayArea'        => 'insideGroup',
                        'sortOrder'          => $sortOrder
                    ],
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    private function getTargetProductGridModalConfig()
    {
        $listingTarget = self::DATA_SCOPE . '_product_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'options'       => [
                            'title' => 'Choose...',
                        ]
                    ]
                ]
            ],
            'children'  => [
                'product_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType'      => 'insertListing',
                                'autoRender'         => false,
                                'dataLinks'          => [
                                    'imports' => false,
                                    'exports' => false
                                ],
                                'realTimeLink'       => false,
                                'externalProvider'   => $listingTarget . '.' . $listingTarget . '_data_source',
                                'ns'                 => $listingTarget,
                                'externalFilterMode' => true,
                                'imports'            => [
                                    'productId' => '${ $.provider }:data.product.current_product_id'
                                ],
                                'exports'            => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Retrieve categories tree
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCategoryTree()
    {
        $categoryTree = $this->cacheManager->load(self::TARGET_CATEGORY_TREE_CACHE_ID);

        if ($categoryTree) {
            return $this->serializer->unserialize($categoryTree);
        }

        /* @var $categoryCollection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $categoryCollection = $this->categoryCollectionFactory->create();

        $categoryCollection
            ->setStoreId($this->locator->getStore()->getId())
            ->addAttributeToFilter('level', ['neq' => 0])
            ->addAttributeToSelect(['name', 'parent_id']);

        $categoryById = [
            CategoryModel::TREE_ROOT_ID => [
                'value'    => CategoryModel::TREE_ROOT_ID,
                'optgroup' => null
            ],
        ];

        foreach ($categoryCollection as $category) {

            foreach ([$category->getId(), $category->getParentId()] as $categoryId) {

                if (!isset($categoryById[$categoryId])) {
                    $categoryById[$categoryId] = ['value' => $categoryId];
                }
            }

            $categoryById[$category->getId()]['label']            = $category->getName();
            $categoryById[$category->getParentId()]['optgroup'][] = &$categoryById[$category->getId()];
        }

        $this->cacheManager->save(
            $this->serializer->serialize($categoryById[CategoryModel::TREE_ROOT_ID]['optgroup']),
            self::TARGET_CATEGORY_TREE_CACHE_ID,
            [
                CategoryModel::CACHE_TAG,
                \Magento\Framework\App\Cache\Type\Block::CACHE_TAG
            ]
        );

        return $categoryById[CategoryModel::TREE_ROOT_ID]['optgroup'];
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     * @throws \Exception
     */
    private function getCmsPageOptions()
    {
        $cmsPageOptions = $this->cacheManager->load(self::TARGET_CMS_PAGE_OPTIONS_CACHE_ID);

        if ($cmsPageOptions) {
            return $this->serializer->unserialize($cmsPageOptions);
        }

        $options = $this->cmsPageSource->toOptionArray();

        $this->cacheManager->save(
            $this->serializer->serialize($options),
            self::TARGET_CMS_PAGE_OPTIONS_CACHE_ID,
            [
                CmsPageModel::CACHE_TAG,
                \Magento\Framework\App\Cache\Type\Block::CACHE_TAG
            ]
        );

        return $options;
    }
}
