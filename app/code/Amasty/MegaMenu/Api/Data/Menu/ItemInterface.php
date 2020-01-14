<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Api\Data\Menu;

interface ItemInterface
{
    const TABLE_NAME = 'amasty_menu_item_content';

    /**#@+
     * Constants defined for keys of data array
     */
    const ID = 'id';

    const ENTITY_ID = 'entity_id';

    const TYPE = 'type';

    const STORE_ID = 'store_id';

    const NAME = 'name';

    const LABEL = 'label';

    const LABEL_TEXT_COLOR = 'label_text_color';

    const LABEL_BACKGROUND_COLOR = 'label_background_color';

    const STATUS = 'status';

    const CONTENT = 'content';

    const WIDTH = 'width';

    const WIDTH_VALUE = 'width_value';

    const COLUMN_COUNT = 'column_count';

    const SORT_ORDER = 'sort_order';

    /**#@-*/

    const FIELDS_BY_STORE_CUSTOM = [
        'general'               => [
            self::NAME,
            self::WIDTH,
            self::WIDTH_VALUE,
            self::STATUS,
            self::LABEL,
            self::LABEL_TEXT_COLOR,
            self::LABEL_BACKGROUND_COLOR,
            self::SORT_ORDER
        ],
        'am_mega_menu_fieldset' => [
            self::CONTENT
        ]
    ];

    const FIELDS_BY_STORE_CATEGORY = [
        'am_mega_menu_fieldset' => [
            self::WIDTH,
            self::WIDTH_VALUE,
            self::COLUMN_COUNT,
            self::CONTENT,
            self::LABEL,
            self::LABEL_TEXT_COLOR,
            self::LABEL_BACKGROUND_COLOR
        ]
    ];

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setEntityId($entityId);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getLabelTextColor();

    /**
     * @param string $labelColor
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setLabelTextColor($labelColor);

    /**
     * @return string
     */
    public function getLabelBackgroundColor();

    /**
     * @param string $labelColor
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setLabelBackgroundColor($labelColor);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setStatus($status);

    /**
     * @return string|null
     */
    public function getContent();

    /**
     * @return int|null
     */
    public function getWidth();

    /**
     * @param int|null $width
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setWidth($width);

    /**
     * @return int|null
     */
    public function getWidthValue();

    /**
     * @param int|null $width
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setWidthValue($width);

    /**
     * @return int|null
     */
    public function getColumnCount();

    /**
     * @param int|null $columnCount
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setColumnCount($columnCount);

    /**
     * @return int|null
     */
    public function getSortOrder();

    /**
     * @param int|null $sortOrder
     *
     * @return \Amasty\MegaMenu\Api\Data\Menu\ItemInterface
     */
    public function setSortOrder($sortOrder);
}
