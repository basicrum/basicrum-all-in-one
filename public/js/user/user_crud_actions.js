var crudActions = (function($){
    let formId  = '#create-user-form',
        modalId = '#myModal',
        inputFieldClass = '.form-control';

    let resetForm               = function(){
        $(formId).trigger("reset");
    }

    let clearObj    = function(){
        appData.row     = '',
        appData.mode    = '',
        appData.userId  = '';
    }

    let prepareModal            = function(mode){
        resetForm();
        let modalLabel  = 'Create user';
        let buttonLabel = 'Add new user';
        if( mode == 'edit')
        {
            modalLabel  = 'Edit user';
            buttonLabel = 'Save changes';
        }

        $('#modalLabel').html(modalLabel);
        $('#create-user').html(buttonLabel);
    }

    let modalShow               = function(){
        $(modalId).modal('show');
    }

    let addEventListeners       = function(){
        $('body').on('click', '#addBtn', addButtonFunction);
        $('body').on('click', '#editBtn', editButtonFunction);
        $('body').on('click', '#deleteBtn', deleteButtonFunction);
        $('body').on("hidden.bs.modal", "#myModal", modalClose);
        $(formId).on('submit', function(event){ // dont submit the form in case of errors
            event.preventDefault();
        });
    }

    let modalClose = function(){ // on modal close
        $(inputFieldClass).removeClass('error'); // reset error class from input fields
        $('input').each(function(){ // blank all input fields
            $(this).val('');
        });
        clearObj();
    };

    let addButtonFunction       = function(){
        appData.mode = 'add';
        prepareModal('add');
        validationInit();
        modalShow();
    };

    let editButtonFunction      = function(){
        let userId = $(this).data('userid');

        // $('#create-user-form').validate().resetForm();

        appData.mode    = 'edit';
        appData.userId  = userId;
        appData.row     = $(this).parent('td').parent('tr');

        prepareModal('edit');
        validationInit();
        let res = requestAJAX.get(`/admin/user/info/${userId}`)
            .done(function(response){
                let info = JSON.parse(response);
                console.log(info);
                $('#fname').val(info.fname);
                $('#lname').val(info.lname);
                $('#email').val(info.email);

                // $("#user_role option:selected").removeAttr("selected");
                if ( $.inArray("ROLE_ADMIN", info.role) !== -1 )
                {
                    $('#user_role option[value="ROLE_ADMIN"]').prop('selected', true);
                }

                $('#myModal').modal('show');
            });

        modalShow();
    };

    let deleteButtonFunction    = function(){
        let res = requestAJAX.get(`/admin/user/delete/${userId}`)
            .done(function(response){
                let info = JSON.parse(response);
                console.log(info);
                row.remove();
                alert(info.message);
            });
    };

    return {
        addEventListeners: addEventListeners,
    }
})(jQuery);