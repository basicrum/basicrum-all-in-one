var requestAJAX = (function($){
    var sendPost = function(url, formData){
        return $.post(url, formData)
            .fail("POST: Something went wrong! Can not continue!");
    }

    var sendGet = function(url){
        return $.get(url)
            .fail("GET: Something went wrong! Can not continue!");
    }

    return {
        post: sendPost,
        get: sendGet,
    };
}(jQuery));