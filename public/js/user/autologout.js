sessionCountdownTimer = (function(){
    var startTime = 0,
        thresholdTime = 0,
        elementId = '',
        thresholdCallback,
        finalCallback,
        timeLeft = 0,
        interval,
        ajaxEvent = false;

    /**
     * This callback type is called `thresholdCallback`
     * @callback thresholdCallbackParam
     * @param {number} time Time left to zero
     */

    /**
     * This callback type is called `finalCallback`
     * @callback finalCallbackParam Callback to call when 0 is reached
     *
     */

    /**
     * Setup countdown timer
     * @param {number} startTimeParam Time in seconds to start countdown from
     * @param {number} thresholdParam The value when thresholdCallback will be called
     * @param {string} elementIdParam The element on the page, reflects time left to 0. Can be null
     * @param {thresholdCallback} thresholdCallbackParam The callback function is called when lets say 5 minutes left to 0. Can be null
     * @param {finalCallback} finalCallbackParam The callback function is called when 0 is reached. Can be null
     */
    var setup = function(startTimeParam, thresholdParam=null, elementIdParam=null, thresholdCallbackParam=null, finalCallbackParam=null) {
        startTime = startTimeParam;
        timeLeft = startTimeParam;
        thresholdTime = thresholdParam;
        elementId = elementIdParam;
        thresholdCallback = thresholdCallbackParam;
        finalCallback = finalCallbackParam;
        setEvent();
    };

    /**
     * Setup an event
     */
    var setEvent = function(){
        if (ajaxEvent === false) {
            BASIC_RUM_APP.subscribe("dynamic_content_loaded", function(){
                stop();
                reset();
                start();
            });

            ajaxEvent = true;
        }
    };

    /**
     * Start timer
     */
    var start = function() {
        if (startTime > 0) {
            interval = setInterval(function(){
                if (timeLeft > 0) {
                    timeLeft = --timeLeft;
                }
                else {
                    stop();
                    logout();
                }

                if ((timeLeft % 20) === 0) {
                    checkSession();
                }

                if (elementId) {
                    $('#'+elementId).html(secondsToHms(timeLeft));
                }

                if (thresholdTime && thresholdCallback) {
                    if (timeLeft === thresholdTime) {
                        thresholdCallback();
                    }
                }

            }, 1000);
        }
    };

    /**
     * Convert amount of seconds to formatted string H hour(s), i minute(s), s second(s)
     * @param seconds
     * @returns {string} Formatted output H hour(s), i minute(s), s second(s)
     */
    var secondsToHms = function(seconds) {
        seconds = Number(seconds);
        var h = Math.floor(seconds / 3600);
        var m = Math.floor(seconds % 3600 / 60);
        var s = Math.floor(seconds % 3600 % 60);

        var hDisplay = h > 0 ? h + (h === 1 ? " hour, " : " hours, ") : "";
        var mDisplay = m > 0 ? m + (m === 1 ? " minute, " : " minutes, ") : "";
        var sDisplay = s > 0 ? s + (s === 1 ? " second" : " seconds") : "0 seconds";

        return hDisplay + mDisplay + sDisplay;
    };

    /**
     * Stop timer
     */
    var stop = function() {
        clearInterval(interval);
    };

    /**
     * Reset timer to initially passed value
     */
    var reset = function() {
        timeLeft = startTime;
    };

    var checkSession = function() {
        $.get('/admin/user/check-session')
            .done(function(response){
                if ('success' !== response.status ) {
                    alert("Session is died. \nYou will be redirected to login page");
                    logout();
                }
            })
            .fail(function(){
                // TODO: Maybe redirect to logout besides this message ??
                alert('Something went wrong');
            });
    }

    var logout = function(){
        document.location.href = '/logout';
    };

    return {
        setup: setup,
        start: start,
        stop: stop,
        reset: reset,
        checkSession: checkSession,
        setEvent: setEvent,
    };
})();

var sessionTimeoutModal = (function(){
    /**
     * Modal id
     * @type {string}
     */
    var modalId = "#sessionWarningModal";
    var continueButtonEventListener = false;

    /**
     * Show modal
     */
    var showModal = function() {
        setupEventListener();
        $(modalId).modal("show");
    };

    /**
     * Event listener for continue session button
     */
    var setupEventListener = function() {

        // make sure we set it up only once
        if (!continueButtonEventListener) {
            $("#modal-session-continue").on('click', function () {
                sessionCountdownTimer.checkSession();
                sessionCountdownTimer.reset();
                hideModal();
            });
            continueButtonEventListener = true;
        }
    };

    /**
     * Hide modal
     */
    var hideModal = function() {
        $(modalId).modal("hide");
    };

    return {
        show: showModal,
    };
})();

sessionCountdownTimer.setup(1800, 300, 'session_timer', sessionTimeoutModal.show);
sessionCountdownTimer.start();


