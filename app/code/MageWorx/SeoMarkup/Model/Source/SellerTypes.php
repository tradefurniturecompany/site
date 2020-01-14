<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\SeoMarkup\Model\Source;

/**
 * Used in creating options for config value selection
 *
 */
class SellerTypes extends \MageWorx\SeoMarkup\Model\Source
{
    /**
     *
     * {@inheritDoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'LocalBusiness',
                'label' => 'Local Business'
            ],
            [
                'value' => 'Organization',
                'label' => 'Organization'
            ],
            [
                'value' => 'AutoPartsStore',
                'label' => 'Auto Parts Store'
            ],
            [
                'value' => 'BikeStore',
                'label' => 'Bike Store'
            ],
            [
                'value' => 'BookStore',
                'label' => 'Book Store'
            ],
            [
                'value' => 'ClothingStore',
                'label' => 'Clothing Store'
            ],
            [
                'value' => 'ComputerStore',
                'label' => 'Computer Store'
            ],
            [
                'value' => 'ConvenienceStore',
                'label' => 'Convenience Store'
            ],
            [
                'value' => 'DepartmentStore',
                'label' => 'Department Store'
            ],
            [
                'value' => 'ElectronicsStore',
                'label' => 'Electronics Store'
            ],
            [
                'value' => 'Florist',
                'label' => 'Florist'
            ],
            [
                'value' => 'FurnitureStore',
                'label' => 'Furniture Store'
            ],
            [
                'value' => 'GardenStore',
                'label' => 'Garden Store'
            ],
            [
                'value' => 'GroceryStore',
                'label' => 'Grocery Store'
            ],
            [
                'value' => 'HardwareStore',
                'label' => 'Hardware Store'
            ],
            [
                'value' => 'HobbyShop',
                'label' => 'Hobby Shop'
            ],
            [
                'value' => 'HomeGoodsStore',
                'label' => 'Home Goods Store'
            ],
            [
                'value' => 'JewelryStore',
                'label' => 'Jewelry Store'
            ],
            [
                'value' => 'LiquorStore',
                'label' => 'Liquor Store'
            ],
            [
                'value' => 'MensClothingStore',
                'label' => 'Mens Clothing Store'
            ],
            [
                'value' => 'LiquorStore',
                'label' => 'Liquor Store'
            ],
            [
                'value' => 'MobilePhoneStore',
                'label' => 'Mobile Phone Store'
            ],
            [
                'value' => 'MusicStore',
                'label' => 'Music Store'
            ],
            [
                'value' => 'OfficeEquipmentStore',
                'label' => 'Office Equipment Store'
            ],
            [
                'value' => 'OutletStore',
                'label' => 'Outlet Store'
            ],
            [
                'value' => 'PawnShop',
                'label' => 'Pawn Shop'
            ],
            [
                'value' => 'PetStore',
                'label' => 'Pet Store'
            ],
            [
                'value' => 'ShoeStore',
                'label' => 'Shoe Store'
            ],
            [
                'value' => 'SportingGoodsStore',
                'label' => 'Sporting Goods Store'
            ],
            [
                'value' => 'TireShop',
                'label' => 'Tire Shop'
            ],
            [
                'value' => 'WholesaleStore',
                'label' => 'Wholesale Store'
            ]
        ];
    }
}
