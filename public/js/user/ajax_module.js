var requestAJAX = (function($){
    let sendPost = function(url, formData){
        return $.post(url, formData)
            .fail("POST: Something went wrong! Can not continue!");
    }

    let sendGet = function(url){
        return $.get(url)
            .fail("GET: Something went wrong! Can not continue!");
    }

    return {
        post: sendPost,
        get: sendGet,
    };
}(jQuery));