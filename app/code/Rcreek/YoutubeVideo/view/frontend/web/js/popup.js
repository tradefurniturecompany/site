;define(
   [
     'jquery',
     'Magento_Ui/js/modal/modal'
   ],
   function($) {
      "use strict";
      //creating jquery widget
      $.widget('Rcreek_YoutubeVideo.Popup', {
         options: {
            modalForm: '#popup-youtube-video',
            modalButton: '.popup-open'
         },
         _create: function() {
           console.log(this);
             this.options.modalOption = this.getModalOptions();
             this._bind();
         },
         getModalOptions: function() {
             /** * Modal options */
             var options = {
               type: 'popup',
               responsive: true,
               clickableOverlay: true,
               title: this.options.title,
               modalClass: 'popup',
               buttons: [{
                  text: $.mage.__('Close'),
                  class: '',
                  click: function () {
                     this.closeModal();
                  }
               }]
             };
             return options;
         },
          _bind: function(){
             var modalOption = this.options.modalOption;
             var modalForm = this.options.modalForm;
             $(document).on('click', this.options.modalButton, function(){
                $(modalForm).modal(modalOption);
                $(modalForm).trigger('openModal');
             });
          }
      });

      return $.Rcreek_YoutubeVideo.Popup;
   }
);
