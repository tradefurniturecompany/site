define([
        'jquery',
        'IWD_InfinityScroll/js/helper/url',
        'jquery/ui'
    ],

    function ($, urlHelper) {
        'use strict';

        $.fn.scrollEnd = function(callback, timeout) {
            $(this).scroll(function(){
                var $this = $(this);
                if ($this.data('scrollTimeout')) {
                    clearTimeout($this.data('scrollTimeout'));
                }
                $this.data('scrollTimeout', setTimeout(callback,timeout));
            });
        };

        $.widget('mage.iwdInfinityScroll', {
            loadingFlag: false,
            currentPage: 1,
            startedPage: 1,
            firstPage: 1,
            options: {
                selectorToolbar: '.toolbar.toolbar-products',
                selectorToolbarPages: '.toolbar.toolbar-products .pages',
                selectorToolbarLimiter: '.toolbar.toolbar-products .limiter',
                selectorToolbarAmount: '.toolbar.toolbar-products .toolbar-amount',
                selectorContent: '#maincontent > .columns > .column div.products',
                selectorLayered: '#layered-filter-block',

                selectorLoadedNextPages: '#iwd-loaded-pages',
                selectorLoadNextPage: '#iwd-load-next-page',
                selectorUploadedNextPages: '#iwd-loaded-pages .iwd-uploaded-pages',

                selectorLoadedPrevPages: '#iwd-loaded-pages-top',
                selectorLoadPrevPage: '#iwd-load-prev-page',
                selectorUploadedPrevPages: '#iwd-loaded-pages-top .iwd-uploaded-pages',

                uploadMode: 'scroll',
                pageBlockHeader: true,
                pagesCount: 1
            },

            init: function(options) {
                this.initOptions(options);
                this.initAdditionalBlocks();
                this.initLayeredNavigation();
                this.initUploadMode();
            },

            initOptions: function(options) {
                var self = this;
                options = options || {};
                $.each(options, function(i, e){self.options[i] = e;});

                var page = urlHelper.getUrlParam(window.location.href, "p");
                self.firstPage = self.startedPage = self.currentPage = (!page ? 1 : parseInt(page));
            },

            initAdditionalBlocks: function() {
                this.addBlockForPrevPages();
                this.hideToolbar();
                this.addPageWrapper();
                this.addBlockForNextPages();
            },
            addBlockForPrevPages: function () {
                var self = this;
                if (this.options.uploadMode != 'pagination' && self.startedPage != 1 && $(self.options.selectorLoadedPrevPages).length > 0) {
                    $(self.options.selectorContent).prepend($(self.options.selectorLoadedPrevPages));

                    $(document).on('click', self.options.selectorLoadPrevPage, function () {
                        self.uploadPrevPage();
                    });

                    self.appendPageBlockHeadForFirstPage();
                } else {
                    $(self.options.selectorLoadedPrevPages).remove();
                }
            },
            hideToolbar: function() {
                if (this.options.uploadMode != 'pagination') {
                    $(this.options.selectorToolbarPages).css('display', 'none');
                    $(this.options.selectorToolbarLimiter).css('display', 'none');
                    $(this.options.selectorToolbarAmount).css('display', 'none');
                }
            },
            addPageWrapper: function() {
                var pageBlockId = 'iwd_uploaded_page_' + this.currentPage;
                $(this.options.selectorUploadedNextPages).append(
                    '<div id="' + pageBlockId + '" data-page-number="' + this.currentPage + '"></div>'
                );

                return pageBlockId;
            },
            addPageWrapperPrev: function() {
                var pageBlockId = 'iwd_uploaded_page_' + this.startedPage;
                $(this.options.selectorUploadedPrevPages).append(
                    '<div id="' + pageBlockId + '" data-page-number="' + this.startedPage + '"></div>'
                );

                return pageBlockId;
            },
            addBlockForNextPages: function() {
                $(this.options.selectorContent).append($(this.options.selectorLoadedNextPages));
            },

            initLayeredNavigation: function() {
                var self = this;
                $(document).on('click', this.options.selectorLayered + ' a', function (e) {
                    e.preventDefault();
                    var link = $(this).attr('href');
                    if (link) {
                        link = urlHelper.setUrlParam(link, 'p', 1);
                        self.uploadProductsForLayeredNavigation(link);
                    }
                });
            },

            initUploadMode: function() {
                switch(this.options.uploadMode){
                    case 'button':
                        return this.modeButton();
                    case 'scroll':
                        return this.modeScrolling();
                    case 'pagination':
                        return this.modeAjaxPagination();
                    default:
                        return false;
                }
            },

            modeButton: function() {
                var self = this;

                $(window).scrollEnd(function(){
                    self.updateBrowserUrl();
                }, 200);

                $(document).on('click', this.options.selectorLoadNextPage, function () {
                    self.uploadNextPage();
                });
            },

            modeScrolling: function() {
                var self = this;

                $(window).scrollEnd(function(){
                    self.uploadNextPage();
                    self.updateBrowserUrl();
                }, 200);
            },

            modeAjaxPagination: function() {
                var self = this;
                $(document).on('click', this.options.selectorToolbarPages + ' a', function (e) {
                    e.preventDefault();
                    var link = $(this).attr('href');
                    if (link) {
                        self.uploadProductsForPagination(link);
                    }
                });
            },

            uploadNextPage: function() {
                var self = this;

                if (!self.loadingFlag && self.currentPage < self.options.pagesCount) {
                    if ($("#iwd_uploaded_page_" + self.currentPage).offset().top < $(window).scrollTop() + $(window).height()) {
                        var ajaxUrl = urlHelper.setUrlParam(window.location.href, 'p', self.currentPage + 1);
                        self.uploadProducts(ajaxUrl);
                    }
                }
            },

            uploadPrevPage: function() {
                var self = this;

                var ajaxUrl = urlHelper.setUrlParam(window.location.href, 'p', self.startedPage - 1);
                $.ajax({
                    url: ajaxUrl,
                    type: 'post',
                    dataType: 'html',
                    beforeSend: function () {
                        self.showLoaderPrev();
                        $(self.options.selectorLoadPrevPage).css("display", "none");
                    },
                    success: function (response) {
                        self.startedPage--;
                        var pageBlockId = "#" + self.addPageWrapperPrev();

                        /* content */
                        $(pageBlockId).append(
                            $(response).find(self.options.selectorContent +'> .product-items, '+ self.options.selectorContent + '+script')
                        ).promise().done(function(){
                            self.appendPageBlockHead(pageBlockId);
                            self.afterUploadProducts();
                        });

                        /* toolbar */
                        $(self.options.selectorToolbar).each(function (i, toolbar) {
                            var toolbars = $(response).find(self.options.selectorToolbar);
                            if (toolbars[i]) {
                                $(toolbar).replaceWith( $(toolbars[i])).promise().done(function(){
                                    $(self.options.selectorToolbar).trigger('contentUpdated');
                                });
                            }
                        });
                        self.hideToolbar();

                        self.changeUrlInBrowser(ajaxUrl);
                    },
                    error: function (jqXHR, status, error) {
                        window.console && console.log(status + ': ' + error + "\nResponse text:\n" + jqXHR.responseText);
                    },
                    complete: function () {
                        self.hideLoaderPrev();
                        if (self.startedPage > 1) {
                            $(self.options.selectorLoadPrevPage).css("display", "block");
                        } else {
                            $(self.options.selectorLoadPrevPage).css("display", "none");
                        }
                    }
                });
            },

            updateBrowserUrl: function() {
                var viewingPageId = this.getPageIdViewingNow();
                var url = urlHelper.setUrlParam(window.location.href, 'p', viewingPageId);
                this.changeUrlInBrowser(url);
            },

            getPageIdViewingNow: function () {
                if (this.currentPage <= this.startedPage) {
                    return this.startedPage;
                }

                for (var i = this.startedPage; i <= this.currentPage; i++) {
                    var elem = $("#iwd_uploaded_page_" + i);
                    var elemTop = elem.offset().top;
                    var elemBottom = elemTop + elem.height();
                    if (elemTop <= $(window).scrollTop() && elemBottom > $(window).scrollTop())
                        return i;
                }

                return this.firstPage;
            },

            uploadProducts: function (link) {
                var self = this;

                $.ajax({
                    url: link,
                    type: 'post',
                    dataType: 'html',
                    beforeSend: function () {
                        self.beforeAjaxLoadPage();
                    },
                    success: function (response) {
                        self.currentPage++;
                        var pageBlockId = "#" + self.addPageWrapper();

                        $(pageBlockId).append(
                            $(response).find(self.options.selectorContent +'> .product-items, '+ self.options.selectorContent + '+script')
                        ).promise().done(function(){
                            self.appendPageBlockHead(pageBlockId);
                            self.afterUploadProducts();
                        });

                        self.changeUrlInBrowser(link);
                    },
                    error: function (jqXHR, status, error) {
                        window.console && console.log(status + ': ' + error + "\nResponse text:\n" + jqXHR.responseText);
                    },
                    complete: function () {
                        self.afterAjaxLoadPage();
                    }
                });
            },

            uploadProductsForLayeredNavigation: function (link) {
                var self = this;

                $.ajax({
                    url: link,
                    type: 'post',
                    dataType: 'html',
                    showLoader: true,
                    beforeSend: function () {
                        self.beforeAjaxLoadPage();
                        self.hideLoaderNext();
                    },
                    success: function (response) {
                        self.currentPage = 1;

                        $(self.options.selectorUploadedNextPages + ' > div').remove();
                        $(self.options.selectorUploadedPrevPages + ' > div').remove();

                        /* pagesCount */
                        var expr = new RegExp(/infinityScroll\.init\((.*)\)/g);
                        var options = expr.exec(response);
                        options = (options[1]) ? $.parseJSON(options[1]) : {};
                        self.options.pagesCount = options['pagesCount'] ? options['pagesCount'] : self.options.pagesCount;

                        /* content */
                        $(self.options.selectorContent + ' > .product-items').remove();
                        $(self.options.selectorContent).prepend(
                            $(response).find(self.options.selectorContent +' > .product-items, '+ self.options.selectorContent + '+script')
                        ).promise().done(function(){
                            self.addPageWrapper();
                            self.afterUploadProducts();
                        });

                        /* toolbar */
                        $(self.options.selectorToolbar).each(function (i, toolbar) {
                            var toolbars = $(response).find(self.options.selectorToolbar);
                            if (toolbars[i]) {
                                $(toolbar).replaceWith( $(toolbars[i])).promise().done(function(){
                                    $(self.options.selectorToolbar).trigger('contentUpdated');
                                });
                            }
                        });
                        self.hideToolbar();

                        /* layered */
                        $(self.options.selectorLayered).html(
                            $(response).find(self.options.selectorLayered).html()
                        ).promise().done(function(){
                            $('#layered-filter-block').trigger('contentUpdated');
                        });

                        self.changeUrlInBrowser(link);
                    },
                    error: function (jqXHR, status, error) {
                        window.console && console.log(status + ': ' + error + "\nResponse text:\n" + jqXHR.responseText);
                    },
                    complete: function () {
                        self.afterAjaxLoadPage();
                    }
                });
            },

            uploadProductsForPagination: function (link) {
                var self = this;

                $.ajax({
                    url: link,
                    type: 'post',
                    dataType: 'html',
                    showLoader: true,
                    beforeSend: function () {
                        self.beforeAjaxLoadPage();
                    },
                    success: function (response) {
                        var page = urlHelper.getUrlParam(link, 'p');
                        self.currentPage = (!page ? 1 : parseInt(page));

                        /* content */
                        $(self.options.selectorContent + ' > .product-items').remove();
                        $(self.options.selectorContent).prepend(
                            $(response).find(self.options.selectorContent +' > .product-items, '+ self.options.selectorContent + '+script')
                        ).promise().done(function(){
                            self.afterUploadProducts();
                        });

                        /* toolbar */
                        $(self.options.selectorToolbar).each(function (i, toolbar) {
                            var toolbars = $(response).find(self.options.selectorToolbar);
                            if (toolbars[i]) {
                                $(toolbar).replaceWith( $(toolbars[i])).promise().done(function(){
                                    $(self.options.selectorToolbar).trigger('contentUpdated');
                                });
                            }
                        });

                        self.changeUrlInBrowser(link);
                    },
                    error: function (jqXHR, status, error) {
                        window.console && console.log(status + ': ' + error + "\nResponse text:\n" + jqXHR.responseText);
                    },
                    complete: function () {
                        self.afterAjaxLoadPage();
                    }
                });
            },

            appendPageBlockHead: function(pageBlock) {
                if (this.options.pageBlockHeader) {
                    var pageNumber = $(pageBlock).attr('data-page-number');
                    $(pageBlock).prepend('<div class="page-block-head">Page ' +
                        pageNumber + ' from ' +
                        this.options.pagesCount + '</div>');
                }
            },

            appendPageBlockHeadForFirstPage: function() {
                if (this.options.pageBlockHeader) {
                    $('<div class="page-block-head">Page ' +
                        this.startedPage + ' from ' +
                        this.options.pagesCount + '</div>').insertAfter(this.options.selectorLoadedPrevPages);
                }
            },

            afterUploadProducts: function() {
                // after
                //if (IWD.InfinityScroll.uploadMode == 'scroll') {
                //    IWD.InfinityScroll.scrollAfter();
                //} else if (IWD.InfinityScroll.uploadMode == 'button') {
                //    IWD.InfinityScroll.buttonAfter();
                //}
                jQuery(window).resize();
                window.dispatchEvent(new Event('resize'));
            },

            changeUrlInBrowser: function(url) {
                window.history.pushState('', '', url);
            },

            beforeAjaxLoadPage: function () {
                var self = this;
                self.loadingFlag = true;

                switch(this.options.uploadMode){
                    case 'button':
                        $(self.options.selectorLoadNextPage).css("display", "none");
                        self.showLoaderNext();
                        break;
                    case 'scroll':
                        self.showLoaderNext();
                        break;
                    case 'pagination':
                        break;
                }
            },

            afterAjaxLoadPage: function () {
                var self = this;
                self.loadingFlag = false;

                self.hideLoaderNext();

                switch(this.options.uploadMode){
                    case 'button':
                        if (this.currentPage < this.options.pagesCount) {
                            $(self.options.selectorLoadNextPage).css("display", "block");
                        }
                        break;
                    case 'scroll':
                        break;
                    case 'pagination':
                        break;
                }
            },

            showLoaderNext: function () {
                $('.iwd-load-bar.bottom-bar').css("display", "block");
            },

            hideLoaderNext: function () {
                $('.iwd-load-bar.bottom-bar').css("display", "none");
            },

            showLoaderPrev: function () {
                $('.iwd-load-bar.top-bar').css("display", "block");
            },

            hideLoaderPrev: function () {
                $('.iwd-load-bar.top-bar').css("display", "none");
            }
        });

        return $.mage.iwdInfinityScroll;
    });