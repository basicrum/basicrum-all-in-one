var userActions = (function($){
    var editURL     = '/widgets/widget/update/',
        createURL   = '/widgets/widget/save';

    var saveUser    = function(formId, crud=null){
        var formData    = $(formId).serialize();
        requestAJAX.post(`${editURL}${appData.userId}`, formData)
            .done(function(response){
                var res = JSON.parse(response);
                if (res.status == 'error')
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
                else
                {
                    if ( crud ) // update users table if called from non profile page
                    {
                        tableManager.updateRow(appData.row, res);
                        $(appData.userDetailsModalId).modal('hide')
                    }
                    else
                    {
                        $('.user-name').html(`${res.user.fname} ${res.user.lname}`);
                    }

                    alert(res.message);
                }
            });
    }

    var createUser  = function(){
        formData    = $(appData.modalFormId).serialize();
        requestAJAX.post(createURL, formData)
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
                    $('#user_details').modal('hide');
                    alert(res.message);
                }
            });
    }


    return {
        saveUser:   saveUser,
        createUser: createUser,
    };

})(jQuery);