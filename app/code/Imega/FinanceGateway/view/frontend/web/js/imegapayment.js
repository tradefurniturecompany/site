define(
  [
    'jquery'
  ],
  function($) {

    var baseUrl='https://angus.finance-calculator.co.uk/api/public/';

    var calcData;
    var financeProvider;
    var providerImage;
    var loanAmount;
    var financeOptions;
    var minOrder;
    var maxOrder;
    var widgetTemplate ='';
    var widgetStyle;
    var currency;
    var currencySymbol;
    var styleUrl;
    var styleCss;

    var headerImage;
    var headerText;
    var paragraphText;
    var detailsHeaderText;
    var splitPaymentText;

    var monthlyAmountSet = false;

    var apiKey;
    var vKey;
    var loanAmount;
    var taxAmount;
    var insertMethod;
    var insertElem;
    var orderRef;
    var orderDescription;
    var financeFilter;
    var customerFirstName;
    var customerLastName;
    var customerTelephone;
    var customerEmail;
    var customerHouseNumber;
    var customerStreet;
    var customerTown;
    var customerPostcode;
    var customerRegion;
    var deliveryStreet;
    var deliveryTown;
    var deliveryPostcode;
    var insertMethod;
    var insertElem;
    var productName;
    var demoMode;
    var testMode;
    var kcoToken;
    var kcoType;
    var kcoAuthToken;
    var kcoOrder;
    var autoRedirect;
    var checkoutEnvironment;
    var dob;
    var secondaryFinance;

    var currentDeposit;
    var collectionLocation;


    return {
      init: function(options){
        initOptions(options);
        getFinanceOptions();
      }
    }

    function initOptions(options){
      if (!options.apiKey){
        throw new Error('API key not set');
      }
      apiKey = options.apiKey;
      if (!options.amount){
        throw new Error('Loan amount not set');
      }
      loanAmount = options.amount;

      insertMethod = options.insertion;
      insertElem = options.element;
      orderRef = options.orderRef;
      orderDescription = options.description;
      taxAmount = options.tax ? options.tax : false;
      financeFilter = options.financeFilter ? options.financeFilter : false;

      insertMethod = options.insertion;
      insertElem = options.element;
      productName = options.description;
      baseUrl = options.api ? options.api : baseUrl;
      demoMode = options.demoMode=='on' ? true : false;
      autoRedirect = options.autoRedirect=='on' ? true : false;
      checkoutEnvironment = options.environment ? options.environment : false;
      secondaryFinance = options.secondary_finance ? options.secondary_finance : false;
      collectionLocation = options.collectionLocation ? options.collectionLocation : false;
    }

    function getFinanceOptions(){
      var requestData = {};
      requestData.loan_amount = loanAmount;
      requestData.api_key = apiKey;
      if(productName){
        requestData.product_name = productName;
      }
      if(financeFilter){
        requestData.finance_filter = financeFilter;
      }
      $.get( baseUrl+'finance-options',
      requestData,
      function( data ){
        if(data.kco && data.kco.is_enabled){
          createKlarnaPaymentSession();
        }
        if(data.finance_available===true){
          if(autoRedirect && data.finance_options.length===1 && data.finance_options[0].deposit_options.length==1){
            checkout();
            return;
          }
          setVars(data);
          linkStylesheet();
          if(data.kco && data.kco.is_enabled){
            setKlarnaTemplate(data.kco.legacy_financing);
          }
          setTemplate(widgetStyle);
          setCss();
          populateOptions(financeOptions);
          setMonthlyAmount();
          populateDepositAmounts(financeOptions[0]);
          getFinanceCalculator(financeOptions[0]);
          setListeners();
        } else {
          if(data.kco && data.kco.is_enabled){
            setVars(data);
            linkStylesheet();
            setKlarnaTemplate(data.kco.legacy_financing);
            setCss();
            $('li[data-payment-product="product-repayment-plan"]').remove();
            console.log('ImegaCheckout : '+data.no_finance_reason);
            setListeners();
          } else {
            var checkoutHtml = '<h2>Finance Error</h2><p>Sorry, there was an error processing your request. Finance is not available for this order.</p>';
            insertHtml(checkoutHtml,insertElem, insertMethod);
            console.log('ImegaCheckout : '+data.no_finance_reason);
          }
        }
      });
    }

    function ImegaCheckoutException(value){
      this.message = 'Imega Checkout Exception : '+value;
    }

    function setVars(data){
      calcData=data;
      testMode=data['test_mode'];
      financeProvider=data['finance_provider'];
      minOrder=data['min_max'].min_order;
      maxOrder=data['min_max'].max_order;
      currency = calcData.currency;
      widgetStyle = data.widget_style;
      styleUrl = data['checkout_options'].stylesheet_url;
      financeOptions=data['finance_options'];
      providerImage=data['widget_options'].header_img;
      topText=data['widget_options'].top_text;
      headerText =data['checkout_options'].header_text;
      paragraphText =data['checkout_options'].paragraph_text;
      detailsHeaderText=data['checkout_options'].details_header_text;
      splitPaymentText=data['widget_options'].split_payments_text;
      styleCss=data['checkout_options'].style_css;
      headerImage = data['widget_options'].header_img;
      switch(currency){
        case 'GBP':
        currencySymbol = '£';
        break;
        case 'USD':
        currencySymbol = '$';
        break;
        default:
        currencySymbol = '£';
      }
    }

    function populateOptions(financeOptions){
      $('#imegacheckout #options-select').html('');
      $.each(financeOptions, function(k,v){
        $('#imegacheckout #options-select').append('<option value="'+k+'" data-fcode="'+v.finance_code+'">'+v.name+'</option>');
      });
    };

    function populateDepositAmounts(financeOption){

      $('#imegacheckout #imega-deposit-amount-td').html('');

      var depositOptions=financeOption.deposit_options;
      var deposithtml = '';

      if(depositOptions.length>1){

        deposithtml+='<select id="imega-deposit-amount-select">';
        $.each(depositOptions, function(k,v){
          deposithtml+='<option value="'+v.value+'"';
          if(currentDeposit || currentDeposit===0){
            if(currentDeposit == v.value){
              deposithtml+=' selected="selected"';
            }
          } else if(v.value*100==Math.round(financeOption.default_deposit/10)*10) {
            deposithtml+=' selected="selected"';
          }
          deposithtml+='>'+v.name+'</option>';
        });
        deposithtml+='</select>';
        $('#imegacheckout #imega-deposit-amount-td').append(deposithtml);
        currentDeposit = parseFloat($('#imegacheckout #imega-deposit-amount-select').val());
      } else {
        deposithtml+='<span id="imega-deposit-amount-select">'+depositOptions[0].name+'</span>';
        currentDeposit=parseFloat(depositOptions[0].value);
        $('#imegacheckout #imega-deposit-amount-td').append(deposithtml);
      }



    }

    function setListeners(){
      $('#imegacheckout').on('change','#options-select, #imega-deposit-amount-select', function(){
        currentDeposit = parseFloat($('#imega-deposit-amount-select').val());
        financeOptionsIndex = $('#options-select').val();
        populateDepositAmounts(financeOptions[financeOptionsIndex]);
        getFinanceCalculator(financeOptions[financeOptionsIndex]);
      });

      $('#imegacheckout').on('click', '#imegacheckout-apply-button', function(){
        showLoading();
        checkout();
      });

      $(document).on('click', 'li[data-payment-product]', function(){
        $('div[data-payment-product]').css({opacity:"1"});
        $(this).siblings().removeClass('selected');
        $(this).addClass('selected');
        $('div[data-payment-product]').hide();
        $('div[data-payment-product="'+$(this).attr('data-payment-product')+'"]').show();
      });

      $('div[data-payment-product]').css({opacity:"0"});
      $('li[data-payment-product]').removeClass('selected');
      $('li[data-payment-product]').first().click();

      $('body').on('click', '.button-klarna-checkout', function(){
        authorizeKlarnaPayment($(this).attr('data-payment-product'));
      });

      $('#imegacheckout').on('change','.dob-input-group input', function(){
        dob = $(this).val();
      });
      $('#imegacheckout').on('change', '#imega-deposit-amount-select', function(){
        setSelectedOptions();
      });
      $('#imegacheckout').on('change', '#options-select', function(){
        setSelectedOptions();
      });


      setSelectedOptions();
    }

    function showLoading(){
      document.querySelector('#imegacheckout-apply-button').disabled = true;
      $('#imegacheckout').addClass('loading');
    }

    function getFinanceCalculator(financeOption){
      var purchasePrice = parseFloat(loanAmount);
      var depositFactor = currentDeposit;
      var loadFactor = parseFloat(financeOption['imega_finance_rate'].load_factor);
      var term = parseInt(financeOption['imega_finance_rate'].term);
      var apr = parseFloat(financeOption['imega_finance_rate'].apr);
      var deferralTerm = parseFloat(financeOption['imega_finance_rate'].deferral_term);
      var settlementFee = parseFloat(financeOption['imega_finance_rate'].settlement_fee);
      var processingFee = parseFloat(financeOption['imega_finance_rate'].processing_fee);
      var interestRatePercent = roundPrice(interestRate * 100);
      var depositPercentage = depositFactor*100;
      var depositAmount;
      var creditAmount;
      var installmentAmount;
      var repaymentAmount;
      var totalRepaymentAmount;
      var bnplTotal;
      var interestAmount;
      var calcProcessingFee;
      var interestRate = apr;
      var interestFreeTotal;

      switch(financeProvider){

        case 'deko' :
        case 'klarna' :
        purchasePrice = purchasePrice * 100;
        depositAmount = Math.round(purchasePrice * depositFactor);
        creditAmount = (purchasePrice - depositAmount - (settlementFee*100));
        installmentAmount = Math.round(creditAmount * loadFactor);
        repaymentAmount = installmentAmount*term;
        totalRepaymentAmount = apr==0 ? purchasePrice : repaymentAmount+depositAmount;
        interestAmount = repaymentAmount - creditAmount;

        purchasePrice = (purchasePrice/100).toFixed(2);
        depositAmount = (Math.ceil(depositAmount) / 100).toFixed(2);
        creditAmount = (creditAmount/100).toFixed(2);
        installmentAmount = Math.round(installmentAmount)/100;
        repaymentAmount = (repaymentAmount/100).toFixed(2);
        totalRepaymentAmount = (totalRepaymentAmount/100).toFixed(2);
        interestAmount = interestAmount<0 ? 0 : (interestAmount/100).toFixed(2);
        break;

        case 'duologi' :
        purchasePrice = purchasePrice;
        depositAmount = Math.round(depositFactor*purchasePrice);
        creditAmount = Math.round(purchasePrice - depositAmount - settlementFee);
        installmentAmount = roundPriceDown(creditAmount * loadFactor);
        repaymentAmount = installmentAmount*term;
        totalRepaymentAmount = apr==0 ? Math.floor(purchasePrice) : Math.round(roundPriceDown(repaymentAmount+depositAmount));
        interestAmount = Math.round(repaymentAmount - creditAmount);
        bnplTotal = roundPrice(purchasePrice+settlementFee);
        settlementFee = settlementFee.toFixed(2);
        break;

        case 'v12' :
        depositAmount = depositFactor*purchasePrice;
        creditAmount =  purchasePrice-depositAmount;
        installmentAmount = roundPrice(creditAmount*loadFactor);

        calcProcessingFee = roundPrice(processingFee*creditAmount);
        repaymentAmount = parseFloat(installmentAmount)*parseFloat(term)+parseFloat(calcProcessingFee);
        totalRepaymentAmount = apr==0 ? purchasePrice : parseFloat(repaymentAmount)+parseFloat(depositAmount);
        interestAmount = repaymentAmount - creditAmount;
        interestRate = (((interestAmount / creditAmount) * 100) / (term / 12));
        interestRate = interestRate<0.1 ? 0 : interestRate;
        interestRate = interestRate.toFixed(2);


        calcProcessingFee = roundPrice(processingFee*creditAmount);
        purchasePrice = roundPrice(purchasePrice);
        depositAmount=roundPriceUp(depositAmount);
        creditAmount = roundPriceDown(creditAmount);
        repaymentAmount=roundPrice(repaymentAmount);
        totalRepaymentAmount=roundPrice(totalRepaymentAmount);
        bnplTotal = roundPrice(parseFloat(creditAmount)+parseFloat(settlementFee));
        interestAmount = roundPrice(totalRepaymentAmount-purchasePrice);
        break;

        case 'snapuk' :
        depositAmount = 15;
        creditAmount =  purchasePrice-depositAmount;
        installmentAmount = creditAmount*loadFactor;
        interestAmount = repaymentAmount - creditAmount;

        installmentAmount = roundPriceUp(installmentAmount);
        repaymentAmount = installmentAmount*term;
        totalRepaymentAmount = apr==0 ? purchasePrice : repaymentAmount+depositAmount+processingFee;
        purchasePrice = roundPrice(purchasePrice);
        depositAmount=roundPriceUp(depositAmount);
        creditAmount = roundPriceDown(creditAmount);
        repaymentAmount=roundPrice(repaymentAmount);
        totalRepaymentAmount=roundPrice(totalRepaymentAmount);
        bnplTotal = roundPrice(parseFloat(creditAmount)+parseFloat(settlementFee));
        interestAmount = roundPrice(totalRepaymentAmount-purchasePrice);
        processingFee = roundPrice(processingFee);
        break;

        default :
        depositAmount = depositFactor*purchasePrice;
        creditAmount =  purchasePrice-depositAmount;
        installmentAmount = creditAmount*loadFactor;
        repaymentAmount = installmentAmount*term;
        totalRepaymentAmount = apr==0 ? purchasePrice : repaymentAmount+depositAmount;
        interestAmount = repaymentAmount - creditAmount;

        installmentAmount = roundPriceDown(installmentAmount);
        purchasePrice = roundPrice(purchasePrice);
        depositAmount=roundPriceUp(depositAmount);
        creditAmount = roundPriceDown(creditAmount);
        repaymentAmount=roundPrice(repaymentAmount);
        totalRepaymentAmount=roundPrice(totalRepaymentAmount);
        bnplTotal = roundPrice(parseFloat(creditAmount)+parseFloat(settlementFee));
        interestAmount = roundPrice(totalRepaymentAmount-purchasePrice);
        processingFee = roundPrice(processingFee);
        interestFreeTotal = roundPrice(parseFloat(creditAmount)+parseFloat(processingFee));
        break;
      }

      $('#calc-table-data').html('');
      $('#bnpl-text').html('');

      let rateTypeFields = {
        1: {
          purchaseprice: currencySymbol+roundPrice(loanAmount),
          depositamount: currencySymbol+depositAmount,
          creditamount: currencySymbol+creditAmount,
          installmentamount: currencySymbol+installmentAmount,
          interestamount: currencySymbol+interestAmount,
          term: term,
          interestrate: interestRate+'%',
          apr: apr+'%',
          totalpayable: currencySymbol+totalRepaymentAmount,
          processingfee: currencySymbol+processingFee,
          ifcloancost: currencySymbol+processingFee,
          ifctotalrepayments: currencySymbol+interestFreeTotal,
          custom1: financeOption.imega_finance_rate.custom_1,
          custom2: financeOption.imega_finance_rate.custom_2,
          custom3: financeOption.imega_finance_rate.custom_3,
          bnpltext: ''
        },
        2: {
          purchaseprice: currencySymbol+roundPrice(loanAmount),
          depositamount: currencySymbol+depositAmount,
          creditamount: currencySymbol+creditAmount,
          deferterm: deferralTerm+' Months',
          settlementfee: currencySymbol+settlementFee,
          bnpltotal: currencySymbol+bnplTotal,
          processingfee: currencySymbol+processingFee,
          bnpltext: '<p>Buy Now Pay Later means buy now and pay the total repayable amount in full within the agreed term and you will be charged absolutely no interest. <strong>Should the balance not be paid in full by the end of the agreed term you will automatically enter into a loan agreement with '+term+' equal payments with a representative APR of '+apr+'%</strong>. If you settle earlier than '+term+' months you will only pay the interest accrued over the number of months you have been paying the loan.</p>'
        },
        3: {
          purchaseprice: currencySymbol+roundPrice(loanAmount),
          depositamount: currencySymbol+depositAmount,
          creditamount: currencySymbol+creditAmount,
          installmentamount: currencySymbol+installmentAmount,
          interestamount: currencySymbol+interestAmount,
          term: term,
          interestrate: interestRate+'%',
          apr: apr+'%',
          totalpayable: currencySymbol+totalRepaymentAmount,
          calcprocessingfee: currencySymbol+calcProcessingFee,
          bnpltext: ''
        },
        4: {
          purchaseprice: currencySymbol+roundPrice(loanAmount),
          depositamount: currencySymbol+depositAmount,
          creditamount: currencySymbol+creditAmount,
          installmentamount: currencySymbol+installmentAmount,
          interestamount: currencySymbol+interestAmount,
          term: term,
          deferterm: deferralTerm+' Months',
          interestrate: interestRate+'%',
          apr: apr+'%',
          totalpayable: currencySymbol+totalRepaymentAmount,
          calcprocessingfee: currencySymbol+calcProcessingFee,
          bnpltext: ''
        },
        6: {
          purchaseprice: currencySymbol+roundPrice(loanAmount),
          depositamount: currencySymbol+depositAmount,
          creditamount: currencySymbol+creditAmount,
          installmentamount: currencySymbol+installmentAmount,
          interestamount: currencySymbol+interestAmount,
          term: term,
          deferterm: deferralTerm+' Months',
          interestrate: interestRate+'%',
          apr: apr+'%',
          totalpayable: currencySymbol+totalRepaymentAmount,
          settlementfee: currencySymbol+settlementFee,
          bnpltotal: currencySymbol+bnplTotal,
          bnpltext: '<p>With Buy Now Pay Later, during the deferral period of the agreement you can pay as much or as little as you want, when you want. <strong>If you pay off the finance in full during the deferral period, you will avoid paying any interest on your loan and only an additional charge of a '+currencySymbol+settlementFee+' settlement fee will be required.</strong> If you don\'t pay off your finance in full within your chosen deferral period, fixed monthly repayments begin, which will be for '+term+' months with a '+apr+' APR. The '+apr+' APR will be applied to the outstanding balance of your loan.</p>'
        },
        7: {
          purchaseprice: currencySymbol+roundPrice(loanAmount),
          installmentamount: currencySymbol+installmentAmount,
          term: term,
          totalpayable: currencySymbol+totalRepaymentAmount,
          bnpltext: splitPaymentText
        }
      };
      let rateFields = rateTypeFields[financeOption['imega_finance_rate'].rate_type];
      calcData['widget_options']['calculator_fields'].forEach(function(value){
        let fieldName = value.calculator_fields_alias.replaceAll('-','');
        if(rateFields.hasOwnProperty(fieldName)){
          $('#calc-table-data').append('<tr><th>'+value.name+'</th><td data-imega-calculator-field='+value.calculator_fields_alias+'>'+rateFields[fieldName]+'</td></tr>');
        }
      });
      $('#bnpl-text').html(rateFields.bnpltext);
      $('#imegacheckout').css({'opacity':1});
    }



    function roundPrice(value) {
      return Number(Math.round(value+'e'+2)+'e-'+2).toFixed(2);
    }
    function roundPriceUp(value) {
      return Number(Math.ceil(value+'e'+2)+'e-'+2).toFixed(2);
    }
    function roundPriceDown(value) {
      return Number(Math.floor(value+'e'+2)+'e-'+2).toFixed(2);
    }

    function setMonthlyAmount(){
      financeOptions.forEach(function(option){
        option.real_max_deposit = getRealMaxDeposit(option);
      });
      var financeOption = financeOptions.reduce(function(prev, current) {
        return (((1-prev.real_max_deposit)*prev['imega_finance_rate'].load_factor) < ((1-current.real_max_deposit)*current['imega_finance_rate'].load_factor)) ? prev : current;
      });
      var monthlyAmount;
      switch(financeProvider){
        case 'duologi' :
        monthlyAmount = currencySymbol+roundPriceDown((loanAmount-(Math.round(financeOption.real_max_deposit*loanAmount)))*financeOption['imega_finance_rate'].load_factor);
        break;

        case 'snapuk' :
        monthlyAmount = currencySymbol+roundPriceUp((loanAmount-15)*financeOption['imega_finance_rate'].load_factor);
        break;

        case 'deko' :
        case 'klarna' :
        monthlyAmount = currencySymbol+roundPrice((loanAmount-(financeOption.real_max_deposit*loanAmount))*financeOption['imega_finance_rate'].load_factor);
        break;

        default :
        monthlyAmount = currencySymbol+roundPriceDown((loanAmount-(financeOption.real_max_deposit*loanAmount))*financeOption['imega_finance_rate'].load_factor);
      }
      $('#imega-min-payment-text').text(monthlyAmount);
      $('#imegacheckout-min-payment-text').text(monthlyAmount);
    }

    function getRealMaxDeposit(financeOption){
      deposits = financeOption.deposit_options;
      depositAmounts = [];
      deposits.forEach(function(deposit){
        depositAmounts.push(deposit.value);
      });
      return Math.max.apply(Math, depositAmounts);
    }

    function linkStylesheet(){
      $('head').append('<link rel="stylesheet" href="'+styleUrl+'" type="text/css">');
    }

    function setCss(){
      if(styleCss && styleCss!='null'){
        $('head').append('<style>'+styleCss+'</style>');
      }
    }

    function testModeHtml(){
      if(testMode && !demoMode){
        return '<div style="display: flex;background:#2c2f1669;width: 100%;justify-content: center;color:white;"><span style="font-weight:bold;font-size:19px; color:white">TEST APPLICATION</span></div>';
      } else {
        return '';
      }
    }

    function setTemplate(style){
      widgetTemplate+='<div id="imegacheckout">';
      widgetTemplate+=testModeHtml();
      widgetTemplate+='<div class="imegacheckout-column" id="imegacheckout-loan-details">';
      widgetTemplate+='<table id="imegacheckout-calc-table">';
      widgetTemplate+='<tr>';
      widgetTemplate+='<th>Finance Option</th>';
      widgetTemplate+='<td><select id="options-select"></select></td>';
      widgetTemplate+='</tr>';
      widgetTemplate+='<tr>';
      widgetTemplate+='<th>Deposit Amount</th>';
      widgetTemplate+='<td id="imega-deposit-amount-td">';
      widgetTemplate+='<select id="imega-deposit-amount-select">';
      widgetTemplate+='</select>';
      widgetTemplate+='</td>';
      widgetTemplate+='<tbody id="calc-table-data" style="display: table-row-group"></tbody>';
      widgetTemplate+='</tr>';
      widgetTemplate+='</table>';
      widgetTemplate+='</div>';
      widgetTemplate+='</div>';

      insertHtml(widgetTemplate, calcData.kco && calcData.kco.is_enabled ? '#klarna-instalments' : insertElem, insertMethod);


    }

    function setKlarnaTemplate(legacyFinancing){
      var template = '<div id="klarna-checkout" style="display: flex;justify-content: center;flex-direction: column;text-align: center;">';
      template+='<h2 style="font-family:Klarna Headline;font-weight:bold;font-size:30px;">Pay With Klarna</h2>';
      template+='<h3 style="font-family:Klarna Text;font-size:16px;color:black;margin-top:22px">Choose your payment option:</h3>';
      template+='<ul style="font-family:Klarna Text;font-weight:bold;font-size:16px;margin-bottom: 50px">';
      template+= legacyFinancing ? '<li data-payment-product="product-repayment-plan">Monthly Instalments</li>' : '';
      template+='</ul>';
      template+='<div class="payment-products">';
      if(legacyFinancing){
        template+='<div data-payment-product="product-repayment-plan">';
        template+='<div id="klarna-instalments"></div>';
        template+='</div>';
        template+='</div>';
      }
      template+='</div>';
      insertHtml(template, insertElem, insertMethod);
    }

    function createKlarnaPaymentSession(){
      var requestData= {
        api_key: apiKey,
        v_key: vKey,
        finance_filter: financeFilter,
        klarna_data: {
          locale: 'en-GB',
          purchase_country: 'GB',
          purchase_currency: 'GBP',
          order_amount: loanAmount*100,
          billing_address: {
            email: customerEmail,
            given_name: customerFirstName,
            family_name: customerLastName,
            street_address: customerHouseNumber+' '+customerStreet,
            city: customerTown,
            postal_code: customerPostcode,
            country: 'GB'
          },
          merchant_reference1: orderRef
        }
      };
      $.ajax({
        type: "POST",
        url: baseUrl+'klarna/create-payment-session',
        data: requestData,
        dataType: 'json',
        success: function(data){
          kcoToken = data.token;
          kcoOrder = data.order;
          initKlarnaPaymentsWidgets(data.methods);
        }
      });
    }

    function initKlarnaPaymentsWidgets(methods){
      window.klarnaAsyncCallback = function(){
        Klarna.Payments.init({
          client_token: kcoToken
        });
        methods.forEach(function(method, index){

          let paymentTab = document.createElement('li');
          paymentTab.setAttribute('data-payment-product', 'product-'+method.identifier);
          paymentTab.innerText = (method.identifier[0].toUpperCase() + method.identifier.slice(1)).replaceAll('_', ' ');
          document.querySelector('#klarna-checkout ul').appendChild(paymentTab);

          let paymentHtml = document.createElement('div');
          paymentHtml.setAttribute('data-payment-product', 'product-' + method.identifier);
          paymentHtml.style.opacity = 0;
          let paymentHolder = document.createElement('div');
          paymentHolder.classList.add(['klarna-payments-container', 'product-option-content-holder']);
          paymentHtml.appendChild(paymentHolder);
          let paymentButton = document.createElement('button');
          paymentButton.setAttribute('data-payment-product', method.identifier);
          paymentButton.classList.add('button-klarna-checkout');
          paymentButton.innerText = 'Pay';
          paymentHtml.appendChild(paymentButton);
          document.querySelector('#klarna-checkout .payment-products').appendChild(paymentHtml);

          Klarna.Payments.load({
            container: '.payment-products [data-payment-product="product-' + method.identifier + '"]>div',
            payment_method_category: method.identifier
          }, function (res) {
          //
          });

          if(index == methods.length-1){
            paymentTab.click();
          }
        });
      };
      $.getScript('https://x.klarnacdn.net/kp/lib/v1/api.js');
    }


    function authorizeKlarnaPayment(method){
      Klarna.Payments.authorize({
        payment_method_category: method
      }, function(res) {
        if(res.approved){
          kcoType = method;
          kcoAuthToken = res.authorization_token;
          checkout();
        } else {
          alert('Payment authorization failed.');
        }
      });
    }

    function insertHtml(html, insertElem, insertMethod){
      switch(insertMethod){
        case 'before' :
        $(insertElem).before(html);
        break;
        case 'after'  :
        $(insertElem).after(html);
        break;
        case 'append' :
        $(insertElem).append(html);
        break;
        case 'prepend' :
        $(insertElem).prepend(html);
        case 'replace' :
        $(insertElem).html(html);
        break;
      }
    }

    function checkout(){
      var checkoutRequestData= {
        api_key: apiKey,
        loan_amount: loanAmount,
        finance_code: $('#imegacheckout #options-select option:selected').attr('data-fcode'),
        deposit: currentDeposit,
        order_ref: orderRef,
        order_description: orderDescription
      }

      if(deliveryStreet){
        checkoutRequestData.delivery_street = deliveryStreet;
        checkoutRequestData.delivery_town = deliveryTown;
        checkoutRequestData.delivery_postcode = deliveryPostcode;
      }

      if(taxAmount){
        checkoutRequestData.tax = taxAmount;
      }
      if(demoMode){
        checkoutRequestData.demo_mode = 'on';
      }
      if (checkoutEnvironment){
        checkoutRequestData.environment = checkoutEnvironment;
      }
      if(kcoType){
        checkoutRequestData.kco_token = kcoAuthToken;
        checkoutRequestData.kco_order = kcoOrder;
      }
      if(dob){
        checkoutRequestData.dob = dob;
      }
      if(secondaryFinance){
        checkoutRequestData.secondary_finance = secondaryFinance;
      }
      if(collectionLocation){
        checkoutRequestData.collection_location = collectionLocation;
      }

      window.location.href =baseUrl+'finance-checkout?'+$.param(checkoutRequestData);
    }

    function setSelectedOptions() {
      window.checkoutConfig.payment.immFinanceGateway.financeCode = $('#imegacheckout #options-select option:selected').attr('data-fcode');
      window.checkoutConfig.payment.immFinanceGateway.deposit = currentDeposit;
    }

  });
