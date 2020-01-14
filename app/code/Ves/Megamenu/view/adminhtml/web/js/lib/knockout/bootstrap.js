/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
 /** Loads all available knockout bindings, sets custom template engine, initializes knockout on page */

 define([
    'jquery',
    'ko',
    'Magento_Ui/js/lib/knockout/template/engine',
    'knockoutjs/knockout-es5',
    /*'Magento_Ui/js/lib/knockout/bindings/bootstrap',*/
    'Magento_Ui/js/lib/knockout/extender/observable_array',
    'Magento_Ui/js/lib/knockout/extender/bound-nodes',
    'domReady!'
    ], function ($, ko, templateEngine) {
        'use strict';
        if(!$('body').hasClass('vesmegamenu-menu-edit')){
            // ko.uid = 0;
            // ko.setTemplateEngine(templateEngine);
            // ko.applyBindings();
        } else {
            //ko.uid = 0;

            //ko.setTemplateEngine(templateEngine);
            //ko.applyBindings();
        }
    });