var tableManager = (function(){
    var td = {
        id      : 1,
        fname   : 2,
        lname   : 3,
        email   : 4,
        role    : 5,
    };

    var userRole = function(roles){
        var userRole = 'User';

        if ( $.inArray("ROLE_ADMIN", roles) !== -1 )
        {
            userRole = 'Admin';
        }

        return userRole;
    }

    var updateRow = function(row, data){
        row.children(`td:nth-child(${td.id})`).html(data.user.id);
        row.children(`td:nth-child(${td.fname})`).html(data.user.fname);
        row.children(`td:nth-child(${td.lname})`).html(data.user.lname);
        row.children(`td:nth-child(${td.email})`).html(data.user.email);

        row.children(`td:nth-child(${td.role})`).html(userRole(data.user.roles));
    }

    var insertRow = function(data){
        var row = `<tr>
            <td>${data.user.id}</td>
            <td>${data.user.fname}</td>
            <td>${data.user.lname}</td>
            <td>${data.user.email}</td>
            <td>${userRole(data.user.roles)}</td>
            <td>
                <button
                    id="editBtn"
                    class="btn btn-info"
                    data-userid="${data.user.id}">
                    Edit User
                </button>
                <button
                    id="deleteBtn"
                    class="btn btn-danger"
                    data-userid="${data.user.id}">
                    Delete User
                </button>
            </td>
            </tr>
        `;
        $(appData.usersTableId).prepend(row);
    }

    return {
        updateRow: updateRow,
        insertRow: insertRow,
    };
})();