var userActions = (function($){
    let editURL     = '/admin/user/update/',
        createURL   = '/admin/register/save';

    let saveUser    = function(formId, crud=null){
        let formData    = $(formId).serialize();
        // console.log(formData); return;
        requestAJAX.post(`${editURL}${appData.userId}`, formData)
            .done(function(response){
                console.log(response);
                let res = JSON.parse(response);
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
                        $('#myModal').modal('hide')
                    }
                    else
                    {
                        $('.user-name').html(`${res.user.fname} ${res.user.lname}`);
                    }

                    alert(res.message);
                }
            });
    }

    let createUser  = function(){
        formData    = $("#create-user-form").serialize();
        requestAJAX.post('/admin/register/save', formData)
            .done(function(response){
                console.log(response);
                let res = JSON.parse(response);
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
                    $('#myModal').modal('hide');
                    alert(res.message);
                }
            });
    }


    return {
        saveUser:   saveUser,
        createUser: createUser,
    };

})(jQuery);