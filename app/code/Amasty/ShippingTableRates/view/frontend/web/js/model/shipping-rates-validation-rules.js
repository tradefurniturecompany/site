/*global define*/
define(
    [],
    function () {
        "use strict";
        return {
            getRules: function () {
                return {
                    'postcode': {
                        'required': true
                    },
                    'country_id': {
                        'required': true
                    }
                    // RCREEK - commented these out because even with them set to false 'state' (region) is still
                    // before the changes to postcode will trigger shipping method updates
                    // 'region_id': {
                    //     'required': true
                    // },
                    // 'region_id_input': {
                    //     'required': true
                    // } 
		};
            }
        };
    }
);
