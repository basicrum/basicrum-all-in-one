(function(w) {
    "use strict";

    var impl;

    if (!w.BASIC_RUM_APP) {
        w.BASIC_RUM_APP = {};
    }

    BASIC_RUM_APP = w.BASIC_RUM_APP;

    if (!BASIC_RUM_APP.plugins) {
        BASIC_RUM_APP.plugins = {};
    }

    // What do we put here
    impl = {
        events: { },

        listenerCallbacks: { },

        fireEvent: function(e_name, data) {
            var i, handler, handlers, handlersLen;

            e_name = e_name.toLowerCase();

            if (!this.events.hasOwnProperty(e_name)) {
                return;// false;
            }

            handlers = this.events[e_name];

            // only call handlers at the time of fireEvent (and not handlers that are
            // added during this callback to avoid an infinite loop)
            handlersLen = handlers.length;
            for (i = 0; i < handlersLen; i++) {
                try {
                    handler = handlers[i];
                    handler.fn.call(handler.scope, data, handler.cb_data);
                }
                catch (err) {
                    alert(err);
                    // Add error logic here
                }
            }

            // remove any 'once' handlers now that we've fired all of them
            for (i = 0; i < handlersLen; i++) {
                if (handlers[i].once) {
                    handlers.splice(i, 1);
                    handlersLen--;
                    i--;
                }
            }

        },
    };

    var brum = {

        utils: {
            addListener: function(el, type, fn) {
                var opts = false;
                if (el.addEventListener) {
                    el.addEventListener(type, fn, opts);
                }
                else if (el.attachEvent) {
                    el.attachEvent("on" + type, fn);
                }

                impl.listenerCallbacks[type] = impl.listenerCallbacks[type] || [];

                impl.listenerCallbacks[type].push({ el: el, fn: fn});
            },

            removeListener: function(el, type, fn) {
                var i;

                if (el.removeEventListener) {
                    // NOTE: We don't need to match any other options (e.g. passive)
                    // from addEventListener, as removeEventListener only cares
                    // about captive.
                    el.removeEventListener(type, fn, false);
                }
                else if (el.detachEvent) {
                    el.detachEvent("on" + type, fn);
                }

                if (impl.listenerCallbacks.hasOwnProperty(type)) {
                    for (var i = 0; i < impl.listenerCallbacks[type].length; i++) {
                        if (fn === impl.listenerCallbacks[type][i].fn &&
                            el === impl.listenerCallbacks[type][i].el) {
                            impl.listenerCallbacks[type].splice(i, 1);
                            return;
                        }
                    }
                }
            },
        },

        subscribe: function(e_name, fn, cb_data, cb_scope, once) {
            var i, handler, ev;

            e_name = e_name.toLowerCase();

            if (!impl.events.hasOwnProperty(e_name)) {
                // allow subscriptions before they're registered
                impl.events[e_name] = [];
            }

            // don't allow a handler to be attached more than once to the same event
            for (i = 0; i < impl.events[e_name].length; i++) {
                handler = ev[i];
                if (handler && handler.fn === fn && handler.cb_data === cb_data && handler.scope === cb_scope) {
                    return this;
                }
            }

            impl.events[e_name].push({
                fn: fn,
                cb_data: cb_data || {},
                scope: cb_scope || null,
                once: once || false
            });

            return this;
        },

        fireEvent: function(e_name, data) {
            return impl.fireEvent(e_name, data);
        },

        init: function() {
            // Init plugins
            for (var k in this.plugins) {
                if (this.plugins.hasOwnProperty(k)) {
                    // plugin exists and has an init method
                    if (typeof this.plugins[k].init === "function") {
                        this.plugins[k].init();
                    }
                }
            }
        }
    };

    (function() {
        var ident;
        for (ident in brum) {
            if (brum.hasOwnProperty(ident)) {
                BASIC_RUM_APP[ident] = brum[ident];
            }
        }
    }());
}(window));

function moreControls() {
    var controls = $('.diagram-controls');
    var controlsBtn = $('.more-diagram-controls');

    $(controls).css('overflow', 'visible');
    $(controls).css('height', 'auto');
    $(controls).css('opacity', '0.9');
    $(controlsBtn).children('.fa-angle-down').hide();
    $(controlsBtn).children('.fa-angle-up').show();
    $(controlsBtn).children('.more-less-text').text('Less controls');
}

function lessControls() {
    var controls = $('.diagram-controls');
    var controlsBtn = $('.more-diagram-controls');

    $(controls).css('overflow', 'hidden');
    $(controls).css('height', '68px');
    $(controls).css('opacity', '1');
    $(controlsBtn).children('.fa-angle-down').show();
    $(controlsBtn).children('.fa-angle-up').hide();
    $(controlsBtn).children('.more-less-text').text('More controls');
}