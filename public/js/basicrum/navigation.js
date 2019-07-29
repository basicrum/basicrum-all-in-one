(function() {
    "use strict";

    var impl = {
        loadContent : function() {
            var ajaxUrl = location.hash.replace(/^#/, '');
            if (ajaxUrl.length < 1) {
                ajaxUrl = '/dashboard';
            }

            $('.preloader').show();

            var xhrOrig = new XMLHttpRequest();
            xhrOrig.open('GET', ajaxUrl, true);

            xhrOrig.addEventListener("readystatechange", function () {
                if (xhrOrig.readyState == 4 && xhrOrig.status == 200) {
                    $('#ajax-content').html(xhrOrig.responseText);
                    $('.preloader').hide();

                    //Attach breadcrumbs
                    var ajaxBreadcrumbs = $('#ajax-content nav[aria-label=breadcrumb]');
                    var navBreadcrumbs  = $('header nav[aria-label=breadcrumb]');

                    if (ajaxBreadcrumbs !== undefined && navBreadcrumbs !== undefined) {
                        navBreadcrumbs.html(ajaxBreadcrumbs.html());
                    }
                }
            });

            xhrOrig.send();
        }
    };

    BASIC_RUM_APP.plugins.navigation = {
        init : function() {
            BASIC_RUM_APP.subscribe("navigation_change", impl.loadContent);

            impl.loadContent();

            $(document).on('click','a', function(e) {
                if ($(this).hasClass('ajax-link')) {

                    e.preventDefault();
                    window.location.hash = $(this).attr('href');

                    $('#wrapper').removeClass('toggled');
                    BASIC_RUM_APP.fireEvent('navigation_change');
                }
            });

            $(document).on('click','button', function(e){
                if ($(this).hasClass('ajax-btn')) {
                    e.preventDefault();

                    window.location.hash = $(this).data('link');

                    $('#wrapper').removeClass('toggled');
                    BASIC_RUM_APP.fireEvent('navigation_change');
                }
            });

            return this;
        }
    }

}());

