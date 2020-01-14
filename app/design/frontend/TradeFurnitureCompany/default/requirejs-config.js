var config = {
  map: {
     '*': {
       paymentmodifications: 'Magento_Checkout/js/payments-modification'
     }
  },
    paths: {
    slick: 'js/slick',
    stickyBanner: "js/stickybanner"
  },
  shim: {
    slick: {
      deps: ['jquery']
    },
    stickyBanner: {
      deps: ['jquery']
    }
  }
};