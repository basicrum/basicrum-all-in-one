/*eslint dot-notation:0*/
/**
 * Original source: https://github.com/SOASTA/boomerang/blob/master/plugins/third-party-analytics.js
 *
 * Modified: Removed non relevant code for Google Analytics
 */

/**
\file google-analytics.js

Captures session ids and campaign information from third party analytic vendors installed on the same page
*/

(function() {
	"use strict";

	BOOMR = BOOMR || {};
	BOOMR.plugins = BOOMR.plugins || {};
	if (BOOMR.plugins.TPAnalytics) {
		return;
	}

	var impl = {
		addedVars: [],

		// collect client IDs, default to false
		// overridable by config
		clientids: true,

		// list of params we won't beacon
		// overridable by config
		dropParams: [],

		/**
		 * Google Analytics
		 * For Universal Analytics there is a function named "ga" which is used to retreive the clientid
		 * ref: https://developers.google.com/analytics/devguides/collection/analyticsjs/command-queue-reference
		 * By default the clientid is stored in a cookie named "_ga" for 2 years
		 * ref: https://developers.google.com/analytics/devguides/collection/analyticsjs/cookies-user-id
		 * For Classic GA, we'll parse the "__utma" cookie
		 *
		 * @return {Object} captured metrics
		 */
		googleAnalytics: function() {
			var data = {};
			var w = BOOMR.window;
			var i, param, value, cid, trackers;

			// list of query params that we want to capture
			// ref: https://support.google.com/analytics/answer/1033863
			var QUERY_PARAMS = ["utm_source", "utm_medium", "utm_term", "utm_content", "utm_campaign"];

			if (impl.clientids) {
				// check for google's global "ga" function then get the clientId
				if (typeof w.ga === "function") {
					try {
						w.ga(function(tracker) {
							// tracker may be undefined if using GTM or named trackers
							if (tracker) {
								data["clientid"] = tracker.get("clientId");
							}
						});
						if (!data["clientid"] && typeof w.ga.getAll === "function") {
							// we may have named trackers, the clientid should be the same for all of them
							trackers = w.ga.getAll();
							if (trackers && trackers.length > 0) {
								data["clientid"] = trackers[0].get("clientId");
							}
						}
					}
					catch (err) {
						// "ga" wasn't google analytics?
						BOOMR.addError(err, "TPAnalytics googleAnalytics");
					}
				}
				// if we still don't have the clientid then fallback to cookie parsing
				if (!data["clientid"]) {
					// cookie parsing for "Universal" GA
					// _ga cookie format : GA1.2.XXXXXXXXXX.YYYYYYYYYY
					// where XXXXXXXXXX.YYYYYYYYYY is the clientid
					cid = BOOMR.utils.getCookie("_ga");
					if (cid) {
						cid = cid.split(".");
						if (cid && cid.length === 4) {
							data["clientid"] = cid[2] + "." + cid[3];
						}
					}
					else {
						// cookie parsing for "Classic" GA
						// __utma #########.XXXXXXXXXX.YYYYYYYYYY.##########.##########.#
						// where XXXXXXXXXX.YYYYYYYYYY is the clientid
						cid = BOOMR.utils.getCookie("__utma");
						if (cid) {
							cid = cid.split(".");
							if (cid && cid.length === 6) {
								data["clientid"] = cid[1] + "." + cid[2];
							}
						}
					}
				}
			}

			// capture paramters from the url that are relevant to google analytics
			for (i = 0; i < QUERY_PARAMS.length; i++) {
				param = QUERY_PARAMS[i];
				value = BOOMR.utils.getQueryParamValue(param);
				if (value) {
					data[param] = value;
				}
			}

			return data;
		},

		pageReady: function() {
			this.addedVars = [];

			var vendor, data, key, beaconParam;
			var vendors = {
				"ga": this.googleAnalytics
			};

			for (vendor in vendors) {
				data = vendors[vendor]();
				for (var key in data) {
					var beaconParam = "tp." + vendor + "." + key;
					if (!BOOMR.utils.inArray(beaconParam, this.dropParams)) {
						BOOMR.addVar(beaconParam, data[key]);
						impl.addedVars.push(beaconParam);
					}
				}
			}
			if (this.addedVars.length > 0) {
				BOOMR.sendBeacon();
			}
		},

		onBeacon: function() {
			if (this.addedVars && this.addedVars.length > 0) {
				BOOMR.removeVar(this.addedVars);
				this.addedVars = [];
			}
		}
	};

	BOOMR.plugins.TPAnalytics = {
		init: function(config) {
			BOOMR.utils.pluginConfig(impl, config, "TPAnalytics", ["clientids", "dropParams"]);

			if (!impl.initialized) {
				if (!BOOMR.utils.isArray(impl.dropParams)) {
					impl.dropParams = [];
				}
				BOOMR.subscribe("page_ready", impl.pageReady, null, impl);
				BOOMR.subscribe("onbeacon", impl.onBeacon, null, impl);
				BOOMR.subscribe("prerender_to_visible", impl.pageReady, null, impl);
				impl.initialized = true;
			}

			return this;
		},

		is_complete: function() {
			return true;
		}
	};

}());
