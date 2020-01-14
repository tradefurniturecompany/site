var initedMegamenu = false;
function playMegamenuJs($, alias, mobileTemplate, event, scrolltofixed) {
	var parent_container = '.sections.nav-sections';
	if (mobileTemplate == 3) {
		$('.ves-drill-down-menu').find('.opener').addClass('ves-click');

		$(window).on('load resize',function(e){
			e.preventDefault();
			var back        	= '<div class="hide-submenu"></div>';
			var subHide     	= $(back);
			var subMenu       	= $('.ves-drill-down-menu .submenu');

			// Add submenu hide bar
			if (subHide.children('hide-submenu').length ==0) {
				subHide.prependTo(subMenu);
			}
			var subHideToggle 	= $('.ves-drill-down-menu .hide-submenu');
			// Hide submenu
			subHideToggle.on("click", function() {
				$(this).parent().parent().removeClass('view-submenu');
				$(this).parent().parent().parent().removeClass('view-submenu');
				$(this).parent().parent().parent().parent().parent().parent().parent().removeClass('view-submenu');
				$(this).parent().parent().parent().parent().parent().parent().parent().parent().removeClass('view-submenu');
				$(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().removeClass('view-submenu');
				$(this).parent().hide();

			});

			if ($(window).width() <= 768){

				$('.ves-drill-down-menu').find('.opener').addClass('fa fa-arrow-right').removeClass('opener');
				$('.ves-drill-down-menu').find('.navigation').addClass('navdrilldown').removeClass('navigation');
				$(".ves-drill-down-menu #"+alias+" .ves-click").on('click', function(e) {
					e.preventDefault();
					if ($(window).width() <= 768){

						$(this).removeClass('.item-active');
						$(this).parents('.dropdown-submenu').addClass('view-submenu');
						$(this).parents('.submenu').addClass('view-submenu');
						$(this).parents('ul.ves-megamenu').addClass('view-submenu');
						var a = $(this).parents('li.nav-item').offset().top;
						var b = $(this).parents('ul.ves-megamenu').offset().top;
						var c = $(this).parent().parent().offset().top;

						$(this).parents('li.nav-item').children('.submenu').css('top',b-a+'px');
						$(this).parent().parent().children('.submenu').css('top',b-c+'px');
						$('.submenu.dropdown-menu').hide();
						$(this).parents('.submenu').show();
						$(this).parent().parent().children('.submenu').show();
						return false;

					}
				});
			}else {
				$('.ves-drill-down-menu').find('.fa-arrow-right').addClass('opener').removeClass('fa fa-arrow-right');
				$('.ves-drill-down-menu').find('.navdrilldown').addClass('navigation').removeClass('navdrilldown');
			}
		});//end load resize window
	}
	jQuery("#"+alias+"-menu .ves-megamenu .level0").hover(function() {
		var mParentTop = jQuery(this).parents('.ves-megamenu').offset().top;
		var mParentHeight = $(this).parent().height();
		var mTop =  $(this).height();
		var mHeight = $(this).height();
		var mParent = $(this).parent();
		if (mHeight < mParentHeight) {
			mTop = $(this).offset().top - mParent.offset().top + mHeight;
		}
		$(this).children('.submenu').css({top:mTop});
	});

	//RCREEK the scrolltofixed code breaks the mobile menu
	if(scrolltofixed && $(window).width() >= 768){ //check option scroll to fixed enabled
		$('.nav-sections-items > .nav-sections-item-content').scrollToFixed({
			zIndex: 99
		});

		$(window).on("resize load", function(){
			if ($(window).width() < 768){
				$('.nav-sections-items > .nav-sections-item-content').css({position: '', top: '', width: '100%'});
			}
		});
		if($("#"+alias+"-menu").parents(parent_container).length > 0) {
			var menuParentPosition = $("#"+alias+"-menu").parents(parent_container).offset().top;
			$(window).scroll(function() {
				var height = $(window).scrollTop();
				if (height<(menuParentPosition) - $("#"+alias+"-menu").outerHeight()) {
					$('.nav-sections-items > .nav-sections-item-content').css({position: '', top: '', width: '100%'});
				}
				$('.section-items.nav-sections-items').find('div').each(function(index, el) {
					if ($(this).html() == '' && $(this).attr('class')=='') {
						$(this).remove();
					}
				});
			});
		}
	}//end check scroll to fixed

	jQuery('p').each(function() {
		var $this = $(this);
		if ($this.html().replace(/\s|&nbsp;/g, '').length == 0)
		$this.remove();
	});
	var toggle_nav = $("#"+alias).attr("data-toggle-mobile-nav");
	if(toggle_nav == true || toggle_nav == 'true' || toggle_nav==1){
		if(!initedMegamenu){
			var menuToogle = function () {
				if ($('html').hasClass('nav-open')) {
					$('html').removeClass('nav-open');
					setTimeout(function () {
						$('html').removeClass('nav-before-open');
					}, 300);
				} else {
					$('body').append('<div class="ves-overlay ves-overlay'+alias+'"></div>');
					$('html').addClass('nav-before-open');
					setTimeout(function () {
						$('html').addClass('nav-open');
					}, 42);
				}
			}
			$(document).on("click", ".action.nav-toggle", menuToogle);
	    }

		$(document).on("click", ".ves-overlay"+alias, function(){
			$("#"+alias).css("left","");
			$('html').removeClass('ves-navopen');
			setTimeout(function () {
				$('html').removeClass('ves-nav-before-open');
			}, 300);
			$(this).remove();
			return false;
		});
	}

	$("#"+alias+" .dynamic-items li").hover(function(){
		$(this).parents(".dynamic-items").find("li").removeClass("dynamic-active");
		$(this).addClass("dynamic-active");
		var id = $(this).data("dynamic-id");
		$("#"+alias+" ."+id).parent().find(".dynamic-item").removeClass("dynamic-active");
		$("#"+alias+" ."+id).addClass("dynamic-active");
	});
	var mImg = '';
	$("#"+alias+" img").hover(function(){
		mImg = '';
		mImg = $(this).attr('src');
		if ($(this).data('hoverimg')){
			$(this).attr('src',$(this).data('hoverimg'));
		}
	},function(){
		$(this).attr('src',mImg);
	});

	$("#"+alias+" li a").hover(function(){
		$(this).css({
			"background-color": $(this).data("hover-bgcolor"),
			"color": $(this).data("hover-color")
		});
	}, function(){
		$(this).css({
			"background-color": $(this).data("bgcolor"),
			"color": $(this).data("color")
		});
	});

	$(window).on("resize load", function(){

		if($("#"+alias).data("disable-bellow") && $("#"+alias).data("disable-above")){
			var window_width = $(window).width();
			if ((window_width <= $("#"+alias).data("disable-bellow")) || (window_width >= $("#"+alias).data("disable-above"))){
				$("#"+alias+"-menu").hide();
			}else{
				$("#"+alias+"-menu").show();
			}

			$("#"+alias).find("li").each(function(index, element){
				if ((window_width <= $(this).data("disable-bellow")) || (window_width >= $(this).data("disable-above"))){
					$(this).addClass("hidden");
				} else if ($(this).hasClass("hidden")){
					$(this).removeClass("hidden");
				}
			});

		} else if($("#"+alias).data("disable-bellow") && !$("#"+alias).data("disable-above")) {
			if ($(window).width() <= $("#"+alias).data("disable-bellow")){
				$("#"+alias+"-menu").hide();
			}else{
				$("#"+alias+"-menu").show();
			}

			$("#"+alias).find("li").each(function(index, element){
				if ($(window).width() <= $(this).data("disable-bellow")){
					$(this).addClass("hidden");
				}else if ($(this).hasClass("hidden")){
					$(this).removeClass("hidden");
				}
			});
		} else if($("#"+alias).data("disable-above") && !$("#"+alias).data("disable-bellow")) {
			if ($(window).width() >= $("#"+alias).data("disable-above")){
				$("#"+alias+"-menu").hide();
			}else{
				$("#"+alias+"-menu").show();
			}

			$("#"+alias).find("li").each(function(index, element){
				if($(window).width() >= $(this).data("disable-above")) {
					$(this).addClass("hidden");
				} else if ($(this).hasClass("hidden")){
					$(this).removeClass("hidden");
				}
			});
		}

		if ($(window).width() >= 768 && $(window).width() <= 1024){
			$("#"+alias+" .nav-anchor").off().click(function(){
				var iParent = $(this).parent('.nav-item');
				iParent.addClass("clicked");
				if ($(iParent).children('.submenu').length == 1){
					iParent.trigger('hover');
					if (iParent.hasClass('submenu-alignleft') || iParent.hasClass('submenu-alignright')){
						if ((iParent.offset().left + iParent.find('.submenu').eq(0).width()) > $(window).width()){
							iParent.find('.submenu').eq(0).css('max-width','100%');
							iParent.css('position','static');
						}
					}
					return false;
				}
			});
		}else{
			$("#"+alias).find('.submenu').css('max-width','');
			$("#"+alias).find('.submenu-alignleft').css('position','relative');
		}
		if ($(window).width() <= 768){
			if($(parent_container).length > 0) {
				$(parent_container).removeAttr( "style" );
			}
			$("#"+alias).addClass("nav-mobile");
		}else{
			$("#"+alias).find(".submenu").css({'display':''});
			$("#"+alias).find("div").removeClass("mbactive");
			$("#"+alias).removeClass("nav-mobile");
		}
	}).resize();

	//Toggle mobile menu
	$('.ves-megamenu-mobile #'+alias+' .opener').on('click', function(e) {
		e.preventDefault();
		$("#"+alias+" .nav-item").removeClass("item-active");
		var parent = $(this).parents(".nav-item").eq(0);
		$(this).toggleClass('item-active');
		if(!$(parent).find(".submenu").eq(0).hasClass("submenu-active")){
			$(parent).find(".submenu").eq(0).css({"display":"none"});
		}
		$(parent).find(".submenu").eq(0).slideToggle();
		$(parent).find(".submenu").eq(0).toggleClass('submenu-active');
		return false;
	});

	if(event == 'hover'){

	} else {
		$(document).mouseup(function(e) {
			var container = $("#"+alias+" .nav-item.level0.current");
		    var container1 = $("#"+alias+" .nav-item.level1.current");
		    var container2 = $("#"+alias+" .nav-item.level2.current");
		    var container3 = $("#"+alias+" .nav-item.level3.current");
		    var container4 = $("#"+alias+" .nav-item.level4.current");
		    var container5 = $("#"+alias+" .nav-item.level5.current");
		    var container6 = $("#"+alias+" .nav-item.level6.current");
		    // if the target of the click isn't the container nor a descendant of the container
		    if (!container.is(e.target) && container.has(e.target).length === 0)
		    {
		        $(container).removeClass('current');
		        $(container).find(".nav-anchor").removeClass("actived");
		        if ($(container).data('caret')) {
					$(container).children('.nav-anchor').find('.ves-caret').removeClass($(container).data('hovercaret')).addClass($(container).data('caret'));
				}
				if($(container).find(".nav-item.current").length > 0){
					$(container).find(".nav-item.current").removeClass("current");
				}
		        return;
		    }
		    if (!container1.is(e.target) && container1.has(e.target).length === 0)
		    {
		        $(container1).removeClass('current');
		        $(container1).find(".nav-anchor").removeClass("actived");
		        if($(container1).find(".nav-item.current").length > 0){
					$(container1).find(".nav-item.current").removeClass("current");
				}
		        return;
		    }
		    if (!container2.is(e.target) && container2.has(e.target).length === 0)
		    {
		        $(container2).removeClass('current');
		        $(container2).find(".nav-anchor").removeClass("actived");
		        if($(container2).find(".nav-item.current").length > 0){
					$(container2).find(".nav-item.current").removeClass("current");
				}
		        return;
		    }
		    if (!container3.is(e.target) && container3.has(e.target).length === 0)
		    {
		        $(container3).removeClass('current');
		        $(container3).find(".nav-anchor").removeClass("actived");
		        if($(container3).find(".nav-item.current").length > 0){
					$(container3).find(".nav-item.current").removeClass("current");
				}
		        return;
		    }
		    if (!container4.is(e.target) && container4.has(e.target).length === 0)
		    {
		        $(container4).removeClass('current');
		        $(container4).find(".nav-anchor").removeClass("actived");
		        if($(container4).find(".nav-item.current").length > 0){
					$(container4).find(".nav-item.current").removeClass("current");
				}
		        return;
		    }
		    if (!container5.is(e.target) && container5.has(e.target).length === 0)
		    {
		        $(container5).removeClass('current');
		        $(container5).find(".nav-anchor").removeClass("actived");
		        if($(container5).find(".nav-item.current").length > 0){
					$(container5).find(".nav-item.current").removeClass("current");
				}
		        return;
		    }
		    if (!container6.is(e.target) && container6.has(e.target).length === 0)
		    {
		        $(container6).removeClass('current');
		        $(container6).find(".nav-anchor").removeClass("actived");
		        if($(container6).find(".nav-item.current").length > 0){
					$(container6).find(".nav-item.current").removeClass("current");
				}
		        return;
		    }
		});
		var container = $("#"+alias+" .nav-item > .dropdown-menu");
		container.each(function(){
			$(this).parent().addClass("menu-has-children");
		})
		$("#"+alias+" .nav-item > .nav-anchor").click(function(e) {
			if ($(window).width() < 768) {
				return true;
			}
			$(this).toggleClass('item-active');
			if($(this).hasClass("actived")) {
				var obj = $(this).parents(".nav-item").eq(0);
				if ($(obj).children('.submenu').length > 0 && !$(obj).hasClass("subgroup")) {
					e.preventDefault();
					$(this).removeClass("actived");
					if($(obj).hasClass('current')) {
						$(obj).removeClass('current');
					}
					if ($(obj).data('caret')) {
						$(obj).children('.nav-anchor').find('.ves-caret').removeClass($(obj).data('hovercaret')).addClass($(obj).data('caret'));
					}

					var container = $("#"+alias+" .nav-item.level0.current");
				    var container1 = $("#"+alias+" .nav-item.level1.current");
				    var container2 = $("#"+alias+" .nav-item.level2.current");
				    var container3 = $("#"+alias+" .nav-item.level3.current");
				    var container4 = $("#"+alias+" .nav-item.level4.current");
				    var container5 = $("#"+alias+" .nav-item.level5.current");
				    var container6 = $("#"+alias+" .nav-item.level6.current");
				    // if the target of the click isn't the container nor a descendant of the container

				    if (!container.is(e.target) && container.has(e.target).length === 0)
				    {
				        $(container).removeClass('current');
				        return false;
				    }
				    if (!container1.is(e.target) && container1.has(e.target).length === 0)
				    {
				        $(container1).removeClass('current');
				        return false;
				    }
				    if (!container2.is(e.target) && container2.has(e.target).length === 0)
				    {
				        $(container2).removeClass('current');
				        return false;
				    }
				    if (!container3.is(e.target) && container3.has(e.target).length === 0)
				    {
				        $(container3).removeClass('current');
				        return false;
				    }
				    if (!container4.is(e.target) && container4.has(e.target).length === 0)
				    {
				        $(container4).removeClass('current');
				        return false;
				    }
				    if (!container5.is(e.target) && container5.has(e.target).length === 0)
				    {
				        $(container5).removeClass('current');
				        return false;
				    }
				    if (!container6.is(e.target) && container6.has(e.target).length === 0)
				    {
				        $(container6).removeClass('current');
				        return false;
				    }
					return false;
				}
			} else {
				var obj = $(this).parents(".nav-item").eq(0);
				if ($(obj).children('.submenu').length > 0 && !$(obj).hasClass("subgroup")) {
					if ($(obj).hasClass('level0')) {
						$('#'+alias+' > .nav-item').removeClass('current');
					}
					if($(obj).hasClass('current')) {
						$(obj).removeClass('current');
					} else {
						$(obj).addClass('current');
						$(this).addClass("actived");
					}

					if ($(obj).data('hovericon')) {
						$(obj).children('.nav-anchor').find('.item-icon').attr('src', $(obj).data('hovericon'));
					}
					if ($(obj).data('caret') && $(obj).data('hovercaret')) {
						$(obj).children('.nav-anchor').find('.ves-caret').removeClass($(obj).data('caret')).addClass($(obj).data('hovercaret'));
					}

					return false;
				}
			}
		 });
	}
	initedMegamenu = true;
}
