/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
var config = {
	map: {
		"*": {
			"nestable": "Ves_Megamenu/js/jquery.nestable",
			"vesknockoutjs": "Ves_Megamenu/js/vesknockoutjs",
			"vesbrowser": "Ves_Megamenu/js/vesbrowser",
			"mage/backend/bootstrap": "Ves_Megamenu/js/bootstrap",
			"Magento_Ui/js/lib/knockout/bootstrap": "Ves_Megamenu/js/bootstrap"
		}
	},
	"deps": [
		"js/theme",
        "Ves_Megamenu/js/bootstrap",
        "mage/adminhtml/globals"
    ],
    shim: {
        'nestable': {
            'deps': ['jquery']
        },
        'Ves_Megamenu/js/jquery.nestable': {
            'deps': ['jquery']
        }
    }
};