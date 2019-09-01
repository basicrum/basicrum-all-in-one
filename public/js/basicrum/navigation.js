(function() {
    "use strict";

    var impl = {
        hashChange : function() {
            var hashHistory = [window.location.hash];
            var historyLength = window.history.length;

            var length = window.history.length;
            if (hashHistory.length && historyLength == length) {
                BASIC_RUM_APP.fireEvent('load_content');
            }
        }
    };

    BASIC_RUM_APP.plugins.navigation = {

        init : function() {
            BASIC_RUM_APP.utils.addListener(window, "hashchange", function() { BASIC_RUM_APP.fireEvent("navigate_by_hash"); });

            BASIC_RUM_APP.subscribe("navigate_by_hash", impl.hashChange);

            $(document).on('click','a', function(e) {
                if ($(this).hasClass('ajax-link')) {

                    e.preventDefault();

                    window.location.hash = $(this).attr('href');
                    BASIC_RUM_APP.plugins.navigation.hash_is_changing_from_me = false;

                    $('#wrapper').removeClass('toggled');
                }
            });

            $(document).on('click','button', function(e){
                if ($(this).hasClass('ajax-btn')) {
                    e.preventDefault();

                    window.location.hash = $(this).data('link');

                    $('#wrapper').removeClass('toggled');
                }
            });

            return this;
        }
    }

}());

