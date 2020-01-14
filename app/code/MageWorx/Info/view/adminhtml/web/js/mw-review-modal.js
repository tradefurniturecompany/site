define([
        "jquery", "Magento_Ui/js/modal/modal", 'jquery/ui', 'mage/translate'
    ], function($){
        var MwReviewModal = {
            initModal: function(config, elemen) {
                var target = $(config.target);
                target.modal({
                    modalClass: 'mageworx-modal-review',

                    title: $.mage.__('Thank you for your feedback!'),

                    opened: function(){
                        $('#mwReviewPopup').css('display', 'block');
                    },
                    closed: function(){
                        var data = $('#review_data').serialize();
                        $.ajax({
                            url: config.dataUrl,
                            type: 'POST',
                            showLoader: true,
                            dataType: 'json',
                            data: data,
                            error: function (xhr, status, errorThrown) {
                                console.log('Error happens. Try again.');
                            }
                        });
                    },

                    buttons: [{
                        text: $.mage.__('Send'),
                        class: 'mw-ext__submit__button',
                        attr: {},

                        click: function (event) {
                            this.closeModal(event);
                        }
                    }]
                });
                var element = $(elemen);

                element.click(function() {
                    target.modal('openModal');
                });
            },
        };

        return {
            'mw-review-modal': MwReviewModal.initModal
        };
    }
);