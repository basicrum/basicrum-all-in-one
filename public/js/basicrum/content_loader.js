(function() {
    "use strict";

    var impl = {
        loadContent : function() {
            var ajaxUrl = location.hash.replace(/^#/, '');
            if (ajaxUrl.length < 1) {
                ajaxUrl = '/dashboard';
            }

            $('.preloader').show();
            // remove modal if it is there
            $('#myModal').remove();

            var xhrOrig = new XMLHttpRequest();
            xhrOrig.open('POST', ajaxUrl, true);
            xhrOrig.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );


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

                    BASIC_RUM_APP.fireEvent('dynamic_content_loaded');
                }
            });

            var data = 'BUMP_NOW_DATE='+ window['BUMP_NOW_DATE'];

            xhrOrig.send(encodeURI(data));
        }
    };

    BASIC_RUM_APP.plugins.content_loader = {
        init : function() {
            BASIC_RUM_APP.subscribe("load_content", impl.loadContent);

            impl.loadContent();

            return this;
        }
    }

}());

