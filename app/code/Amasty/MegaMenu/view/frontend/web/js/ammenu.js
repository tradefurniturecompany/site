define([
    'jquery',
    'jquery/ui',
    'mage/accordion',
    'jquery/jquery.mobile.custom'
], function ($, ui, accordion) {

    $.widget('amasty.megaMenu', {
        options: {
            hambStatus: 0,
            desktopStatus: 0,
            stickyStatus: 0
        },

        _create: function () {
            var self = this,
                isMobile = $(window).width() <= 768 ? 1 : 0;
                isDesktop = this.options.desktopStatus,
                isHamb = this.options.hambStatus,
                isSticky = this.options.stickyStatus;

            $('[data-ammenu-js="menu-toggle"]').off('click').on('click', function () {
                self.toggleMenu();
            });

            if (!isHamb) {
                $('[data-ammenu-js="menu-overlay"]').on('swipeleft click', function () {
                    self.toggleMenu();
                });

                $('[data-ammenu-js="tab-content"]').on('swipeleft', function () {
                    self.toggleMenu();
                });

                $('[data-ammenu-js="submenu-toggle"]').on('click', function (event) {
                    event.preventDefault();

                    $(this)
                        .toggleClass('-down')
                        .parents('.ammenu-link').siblings('.ammenu-item').slideToggle(50);
                });

                if (isMobile) {
                    $(window).on('swiperight', function (e) {
                        var target = $(e.target);

                        if (e.swipestart.coords[0] < 50
                            && !target.parents().is('.ammenu-nav-sections')
                            && !target.is('.ammenu-nav-sections')) {
                            self.toggleMenu();
                        }
                    });
                }
            }

            if (isDesktop) {
                $('[data-ammenu-js="parent-subitem"]')
                    .on('mouseenter', function () {
                        $(this).children('.ammenu-link').addClass('-hovered');
                        $(this).children('.ammenu-item.-child').css('left', ($(this).find('> .ammenu-link').outerWidth()) + 'px').fadeIn(50);
                    })
                    .on('mouseleave', function () {
                        $(this).children('.ammenu-link').removeClass('-hovered');
                        $(this).children('.ammenu-item.-child').fadeOut(50);

                    });

                if (isSticky) {
                    var menu = $('[data-ammenu-js="desktop-menu"]'),
                        menuOffsetTop = menu.offset().top;

                    $(window).on('scroll', function () {
                        menu.toggleClass('-sticky', window.pageYOffset >= menuOffsetTop);
                    });
                }
            }

            $('[data-ammenu-js="menu-items"]').accordion({
                animate: 50,
                closedState: '-collapsed',
                content: '[data-ammenu-js="collapse-content"]',
                collapsibleElement: '[data-ammenu-js="collapse-content"]',
                trigger: '[data-ammenu-js="collapse-trigger"]',
                collapsible: true,
                active: false
            });

            this.removeEmptyPageBuilderItems();
        },

        toggleMenu: function () {
            $('[data-ammenu-js="menu-toggle"]').toggleClass('-active');
            $('[data-ammenu-js="desktop-menu"]').toggleClass('-hide');
            $('[data-ammenu-js="nav-sections"]').toggleClass('-opened');
            $('[data-ammenu-js="menu-overlay"]').fadeToggle(50);
        },

        removeEmptyPageBuilderItems: function () {
            $('[data-ammenu-js="menu-submenu"]').each(function () {
                var element = $(this),
                    childsPageBuilder = element.find('[data-element="inner"]');

                if (childsPageBuilder.length) {
                    //remove empty child categories
                    $('[data-content-type="ammega_menu_widget"]').each(function () {
                        if (!$(this).children().length) {
                            $(this).remove();
                        }
                    });

                    var isEmpty = true;
                    $(childsPageBuilder).each(function () {
                        if ($(this).children().length) {
                            isEmpty = false;
                            return true;
                        }
                    });

                    if (isEmpty) {
                        element.remove();
                    }
                }
            });
        }
    });

    return $.amasty.megaMenu;
});
