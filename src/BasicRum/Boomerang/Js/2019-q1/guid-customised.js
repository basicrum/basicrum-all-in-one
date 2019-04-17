/**
 * Original source: https://github.com/SOASTA/boomerang/blob/master/plugins/guid.js
 *
 * Modified:
 *  - Added more entropy of guid by "guid + '_' + d.getTime()"
 *  - Sending the guid as parameter together with the beacon data
 */

(function() {

	var impl = {
		expires:  604800,
		cookieName: "GUID"
	};

	BOOMR.plugins.GUID = {
		init: function(config) {
			var properties = ["cookieName", "expires"];
			BOOMR.utils.pluginConfig(impl, config, "GUID", properties);
			BOOMR.info("Initializing plugin GUID " + impl.cookieName, "GUID");

			if (!BOOMR.utils.getCookie(impl.cookieName)) {
				BOOMR.info("Could not find a cookie for " + impl.cookieName, "GUID");

				var guid = BOOMR.utils.generateUUID();

                var d = new Date();
                guid = guid + '_' + d.getTime();

				if (!BOOMR.utils.setCookie(impl.cookieName, guid, impl.expires)) {
					BOOMR.subscribe("before_beacon", function() {
						BOOMR.utils.setCookie(impl.cookieName, guid, impl.expires);
					});
				}

				BOOMR.info("Setting GUID Cookie value to: " + guid + " expiring in: " + impl.expires + "s", "GUID");
			}
			else {
				BOOMR.info("Found a cookie named: " + impl.cookieName + " value: " + BOOMR.utils.getCookie(impl.cookieName), "GUID");
			}

            // Adding GUID to beacon because it easier to transfer this value when we have cross origin request
            BOOMR.addVar("guid", BOOMR.utils.getCookie(impl.cookieName));

			return this;
		},
		is_complete: function() {
			return true;
		}
	};
}(this));
