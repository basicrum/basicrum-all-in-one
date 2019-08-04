(function() {
    "use strict";

    var impl = {
        spawnDiagrams: function () {
            var widgets = document.getElementsByClassName('widget-data-container');

            for (var i=0, len = widgets.length|0; i < len; i=i+1|0) {
                var paramsVarName = widgets[i].getAttribute('data-params-name-var');
                var diagramContainerId = widgets[i].getAttribute('id');

                impl.fetchData(
                    function (diagramContainer, response) {
                        Plotly.newPlot(diagramContainer, response.diagrams, response.layout, {displayModeBar: false});
                    },
                    window[paramsVarName],
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
        }
    }

}());

