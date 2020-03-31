var crudActions = (function($){
    var formId  = appData.modalFormId,
        modalId = appData.itemDetailsModalId,
        inputFieldClass = '.form-control';

    var resetForm               = function(){
        $(formId).trigger("reset");
    }

    var clearObj    = function(){
        appData.row     = '',
        appData.mode    = '',
        appData.itemId  = '';
    }

    var prepareModal            = function(mode){
        resetForm();
        var modalLabel  = 'Create Widget';
        var buttonLabel = 'Add new widget';
        if( mode == 'edit')
        {
            modalLabel  = 'Edit widget';
            buttonLabel = 'Save changes';
        }

        $('#modalLabel').html(modalLabel);
        $('#create-widget').html(buttonLabel);
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
        var itemId = $(this).data('itemid');

        appData.mode    = 'edit';
        appData.itemId  = itemId;
        appData.row     = $(this).parent('td').parent('tr');

        prepareModal('edit');
        validationInit();
        var res = requestAJAX.get(`/widget/info/${itemId}`)
            .done(function(response){
                var info = JSON.parse(response);
                $('#name').val(info.name);
                $('#widget').val(info.widget);

                $('#myModal').modal('show');
            });

        modalShow();
    };

    var deleteButtonFunction    = function(){
        var itemId  = $(this).data('itemid');
        var row     = $(this).parent('td').parent('tr');
        var res     = requestAJAX.get(`/widget/delete/${itemId}`)
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