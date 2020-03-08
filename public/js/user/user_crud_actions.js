var crudActions = (function($){
    var formId  = '#create-user-form',
        modalId = appData.userDetailsModalId,
        inputFieldClass = '.form-control';

    var resetForm               = function(){
        $(formId).trigger("reset");
    }

    var clearObj    = function(){
        appData.row     = '',
        appData.mode    = '',
        appData.userId  = '';
    }

    var prepareModal            = function(mode){
        resetForm();
        var modalLabel  = 'Create user';
        var buttonLabel = 'Add new user';
        if( mode == 'edit')
        {
            modalLabel  = 'Edit user';
            buttonLabel = 'Save changes';
        }

        $('#modalLabel').html(modalLabel);
        $('#create-user').html(buttonLabel);
    }

    var modalShow               = function(){
        $(modalId).modal('show');
    }

    var addEventListeners       = function(){
        $('body').on('click', '#addBtn', addButtonFunction);
        $('body').on('click', '#editBtn', editButtonFunction);
        $('body').on('click', '#deleteBtn', deleteButtonFunction);
        $('body').on("hidden.bs.modal", appData.userDetailsModalId, modalClose);
        $(formId).on('submit', function(event){ // dont submit the form in case of errors
            event.preventDefault();
        });
    }

    var modalClose = function(){ // on modal close
        $(inputFieldClass).removeClass('error'); // reset error class from input fields
        $('input').each(function(){ // blank all input fields
            $(this).val('');
        });
        clearObj();
    };

    var addButtonFunction       = function(){
        appData.mode = 'add';
        prepareModal('add');
        validationInit();
        modalShow();
    };

    var editButtonFunction      = function(){
        var userId = $(this).data('userid');

        appData.mode    = 'edit';
        appData.userId  = userId;
        appData.row     = $(this).parent('td').parent('tr');

        prepareModal('edit');
        validationInit();
        var res = requestAJAX.get(`/admin/user/info/${userId}`)
            .done(function(response){
                var info = JSON.parse(response);
                $('#fname').val(info.fname);
                $('#lname').val(info.lname);
                $('#email').val(info.email);

                if ( $.inArray("ROLE_ADMIN", info.role) !== -1 )
                {
                    $('#user_role option[value="ROLE_ADMIN"]').prop('selected', true);
                }

                $('#myModal').modal('show');
            });

        modalShow();
    };

    var deleteButtonFunction    = function(){
        var res = requestAJAX.get(`/admin/user/delete/${userId}`)
            .done(function(response){
                var info = JSON.parse(response);
                row.remove();
                alert(info.message);
            });
    };

    return {
        addEventListeners: addEventListeners,
    }
})(jQuery);