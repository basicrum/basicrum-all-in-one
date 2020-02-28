var logoutTimer = (function($){

    let innerTimer,
        timeOut = 1000 * 60 * 5; // set timeout to 5 mins


    let setEvent = function(){
        $(document).ajaxSuccess(function() {
            console.log("Triggered AJAX event");
            stopTimer();
            startTimer();
        });
    };

    let startTimer = function(){
        innerTimer = setTimeout('logoutTimer.logOut()', timeOut);
    };

    let stopTimer = function(){
        clearTimeout(innerTimer);
    };

    let logOut = function(){
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

