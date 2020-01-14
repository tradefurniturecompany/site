define([
    'jquery'
], function ($) {
    $.widget('mage.ammenuPager', {
        options: {},

        _create: function () {
            var self = this,
                widget_data = self.options.widget_data;
            $(this. element)
                .on("click", ".pager .item:not(.current) a", function (e) {
                    e.preventDefault();
                    var target = $(e.target);
                    if (!target.is('a')) {
                        target = target.parents('a');
                    }
                    var url = target[0].href,
                        element = this;

                    $.ajax({
                        url: url,
                        data: {'widget_data' : widget_data},
                        type: "get",
                        success: function (response) {
                            if (response["block"] !== undefined) {
                                element = $("[data-ammenu-js='" + widget_data['identifier'] + "']");
                                element.html($(response["block"]).html());
                            }
                        }
                    });

                });
        }
    });

    return $.mage.ammenuPager;
});
