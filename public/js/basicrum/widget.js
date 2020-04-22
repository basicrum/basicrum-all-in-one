(function() {
    "use strict";

    var impl = {
        spawnDiagrams: function (days) {
            var widgets = document.getElementsByClassName('widget-data-container');

            for (var i=0, len = widgets.length|0; i < len; i=i+1|0) {
                widgets[i].classList.add("loading");
                
                var paramsVarName = widgets[i].getAttribute('data-params-name-var');
                
                /** we would like to use copy of original diagram params */
                var params = window[paramsVarName];

                if (days !== undefined) {
                    params.global.data_requirements.period.type  = 'moving';
                    params.global.data_requirements.period.start = days;
                    params.global.data_requirements.period.end   = 'now';
                }

                params.BUMP_NOW_DATE = window['BUMP_NOW_DATE'];

                var diagramContainerId = widgets[i].getAttribute('id');

                impl.fetchData(
                    function (diagramContainer, response) {
                        Plotly.react(diagramContainer, response.diagrams, response.layout, {displayModeBar: false, responsive: true});
                    },
                    params,
                    diagramContainerId
                );
            }
        },
        fetchData: function (callback, params, diagramContainerId) {
            $.ajax('/widget/generate_diagram',
                {
                    method: 'post',
                    data: params,
                    success : function(response) {
                        document.getElementById(diagramContainerId).classList.remove("loading");
                        callback(diagramContainerId, response)
                    }
                }
            );
        }

    };

    BASIC_RUM_APP.plugins.widget = {
        init : function() {
            BASIC_RUM_APP.subscribe("dynamic_content_loaded", impl.spawnDiagrams);

            return this;
        },

        reloadPeriod : function (days) {
            impl.spawnDiagrams(days);

            return this;
        }
    }

}());

