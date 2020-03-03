var logoutTimer = (function($){

    var innerTimer,
        timeOut = 1000 * 60 * 5; // set timeout to 5 mins


    var setEvent = function(){
        $(document).ajaxSuccess(function() {
            stopTimer();
            startTimer();
        });
    };

    var startTimer = function(){
        innerTimer = setTimeout('logoutTimer.logOut()', timeOut);
    };

    var stopTimer = function(){
        clearTimeout(innerTimer);
    };

    var logOut = function(){
        alert("Session is expired!");
        document.location.href = '/logout';
    };

    return {
        setEvent:   setEvent,
        startTimer: startTimer,
        logOut:     logOut,
    };
})(jQuery);

logoutTimer.setEvent();
logoutTimer.startTimer();

