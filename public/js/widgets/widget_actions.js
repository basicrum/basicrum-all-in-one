var itemActions = (function($){
    var editURL     = '/widget/update/',
        createURL   = '/widget/save';

    var saveItem    = function(formId, crud=null){
        var formData    = $(formId).serializeArray(),
            jsonData    = JSON.stringify(appData.jsonEditor.get());
        formData.push({name: "widget", value: jsonData});
        requestAJAX.post(`${editURL}${appData.itemId}`, $.param(formData))
            .done(function(response){
                var res = JSON.parse(response);
                if (res.status == 'error')
                {
                    res.fields.forEach(function(item){
                        appData.validator.showErrors({
                            [item.field]: [item.message]
                        });
                    })
                }
                else
                {
                    if ( crud ) // update table if called from non profile page
                    {
                        tableManager.updateRow(appData.row, res);
                        $(appData.itemDetailsModalId).modal('hide')
                    }

                    alert(res.message);
                }
            });
    }

    var createItem  = function(){
        var formData    = $(appData.modalFormId).serializeArray(),
            jsonData    = JSON.stringify(appData.jsonEditor.get());

        formData.push({name: "widget", value: jsonData});
        requestAJAX.post(createURL, $.param(formData))
            .done(function(response){
                var res = JSON.parse(response);
                if (res.status == 'error') // error occured
                {
                    res.fields.forEach(function(item){
                        if (item.field == 'plainPassword')
                        {
                            item.field = 'password';
                        }
                        appData.validator.showErrors({
                            [item.field]: [item.message]
                        });
                    })
                }
                else // no errors,
                {
                    tableManager.insertRow(res);
                    $(appData.itemDetailsModalId).modal('hide');
                    alert(res.message);
                }
            });
    }

    var itemInfo    = function(itemId){
        return requestAJAX.get(`/widget/info/${itemId}`)
    }

    return {
        saveItem:   saveItem,
        createItem: createItem,
        itemInfo:   itemInfo,
    };

})(jQuery);