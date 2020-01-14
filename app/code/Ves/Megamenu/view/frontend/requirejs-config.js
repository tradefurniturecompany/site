/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
 var config = {
 	paths: {
        "menu.bootstrap": "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min",
        "scrolltofixed": "Ves_Megamenu/js/jquery-scrolltofixed-min",
        "megamenuowlcarousel": "Ves_All/lib/owl.carousel/owl.carousel.min",
        "megamenuGeneral": "Ves_Megamenu/js/megamenuGeneral"
    },
    shim: {
        'menu.bootstrap': {
            'deps': ['jquery']
        },
        'scrolltofixed': {
            'deps': ['jquery']
        },
        'megamenuowlcarousel': {
            'deps': ['jquery']
        },
        'megamenuGeneral': {
            'deps': ['jquery']
        },
        'Ves_Megamenu/js/megamenuGeneral': {
            'deps': ['jquery']
        }
    }
};