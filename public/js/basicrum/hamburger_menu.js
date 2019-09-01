(function() {
    "use strict";

    BASIC_RUM_APP.plugins.hamburger_menu = {

        init : function() {
            $('[data-toggle="offcanvas"]').click(function () {
                $('#wrapper').toggleClass('toggled');
            });

            return this;
        }
    }

}());