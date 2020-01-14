define(['jquery'],
    function ($) {
        'use strict';
        return {
            getUrlParam: function(url, key){
                var results = new RegExp('[\?&amp;]' + key + '=([^&amp;#]*)').exec(url);
                if(results) {
                    return results[1] || null;
                }
                return null
            },
            setUrlParam: function (url, key, value) {
                var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                var separator = url.indexOf('?') !== -1 ? "&" : "?";
                if (url.match(re)){
                    return url.replace(re, '$1' + key + "=" + value + '$2');
                } else {
                    var _url = url.split('#');
                    return (_url[1])
                        ? (_url[0] + separator + key + "=" + value + "#" + _url[1])
                        : (_url[0] + separator + key + "=" + value);
                }
            },
            removeUrlParam: function (url, key) {
                var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                if (url.match(re)){
                    return url.replace(re, '');
                }
                return url;
            }
        }
    });