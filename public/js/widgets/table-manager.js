var tableManager = (function(){
    var td = {
        id          : 1,
        name        : 2,
        user        : 3,
        created_at  : 4,
        updated_at  : 5,
    };

    var updateRow = function(row, data){
        row.children(`td:nth-child(${td.id})`).html(data.item.id);
        row.children(`td:nth-child(${td.name})`).html(data.item.name);
        row.children(`td:nth-child(${td.user})`).html(data.item.user_id);
        row.children(`td:nth-child(${td.created_at})`).html(data.item.created_at);
        row.children(`td:nth-child(${td.updated_at})`).html(data.item.updated_at);
    };

    var insertRow = function(data){
        var row = `<tr>
            <td>${data.item.id}</td>
            <td>${data.item.name}</td>
            <td>${data.item.user_id}</td>
            <td>${data.item.created_at}</td>
            <td>${data.item.updated_at}</td>
            <td>
                <button
                    id="editBtn"
                    class="btn btn-info"
                    data-itemid="${data.item.id}">
                    Edit Widget
                </button>
                <button
                    id="deleteBtn"
                    class="btn btn-danger"
                    data-itemid="${data.item.id}">
                    Delete Widget
                </button>
            </td>
            </tr>
        `;
        $(appData.itemsTableId).prepend(row);
    };

    return {
        updateRow: updateRow,
        insertRow: insertRow,
    };
})();