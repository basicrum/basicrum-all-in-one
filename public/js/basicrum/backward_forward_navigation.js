(function() {
    "use strict";

    var impl = {
        backwardOrForwardNavigation : function() {
            var hashHistory = [window.location.hash];
            var historyLength = window.history.length;

            var length = window.history.length;
            if (hashHistory.length && historyLength == length) {
                BASIC_RUM_APP.fireEvent('load_content');
            }
        }
    };

    BASIC_RUM_APP.plugins.backward_forward_navigation = {
        init : function() {
            BASIC_RUM_APP.utils.addListener(window, "hashchange", function() { BASIC_RUM_APP.fireEvent("backward_forward_navigation"); });

            BASIC_RUM_APP.subscribe("backward_forward_navigation", impl.backwardOrForwardNavigation);
            return this;
        }
    }

}());