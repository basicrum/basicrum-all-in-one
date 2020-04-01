var crudActions = (function($){
    var formId          = appData.modalFormId,
        formModalId     = appData.itemDetailsModalId,
        viewModalId     = appData.modalViewId,
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

    var modalShow               = function(modalId){
        $(modalId).modal('show');
    }

    var addEventListeners       = function(){
        $('body').on('click', '#addBtn', addButtonFunction);
        $('body').on('click', '#editBtn', editButtonFunction);
        $('body').on('click', '#deleteBtn', deleteButtonFunction);
        $('body').on('click', '#viewBtn', viewButtonFunction);
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
        modalShow(formModalId);
    };

    var editButtonFunction      = function(){
        var itemId = $(this).data('itemid');

        appData.mode    = 'edit';
        appData.itemId  = itemId;
        appData.row     = $(this).parent('td').parent('tr');

        prepareModal('edit');
        validationInit();
        itemActions.itemInfo(itemId)
            .done(function(response){
                var info = JSON.parse(response);
                $('#name').val(info.name);
                $('#widget').val(info.widget);
            });

        modalShow(formModalId);
    };

    var viewButtonFunction      = function(){
        var itemId = $(this).data('itemid');
        itemActions.itemInfo(itemId)
            .done(function(response){
                var info = JSON.parse(response);
                /*$('#name').val(info.name);
                $('#widget').val(info.widget);*/
                $('#modalWidgetLabel').html(info.name);
                $.ajax('/widget/generate_diagram',{
                    method: 'post',
                    data: JSON.parse(info.widget),
                    success : function(response) {
                        Plotly.react('widget-data-container', response.diagrams, response.layout, {displayModeBar: false, responsive: true})
                    }
                })
            });
        modalShow(viewModalId);
    };

    var deleteButtonFunction    = function(){
        if (confirm('Are you sure you want to delete this widget?')) {
            var itemId  = $(this).data('itemid');
            var row     = $(this).parent('td').parent('tr');
            var res     = requestAJAX.get(`/widget/delete/${itemId}`)
                .done(function(response){
                    var info = JSON.parse(response);
                    row.remove();
                    alert(info.message);
                });
        }
    };

    return {
        addEventListeners: addEventListeners,
    }
})(jQuery);