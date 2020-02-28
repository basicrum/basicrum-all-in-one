var tableManager = (function(){
    let td = {
        id      : 1,
        fname   : 2,
        lname   : 3,
        email   : 4,
        role    : 5,
    };

    let userRole = function(roles){
        let userRole = 'User';

        if ( $.inArray("ROLE_ADMIN", roles) !== -1 )
        {
            userRole = 'Admin';
        }

        return userRole;
    }

    let updateRow = function(row, data){
        console.log(data);
        row.children(`td:nth-child(${td.id})`).html(data.user.id);
        row.children(`td:nth-child(${td.fname})`).html(data.user.fname);
        row.children(`td:nth-child(${td.lname})`).html(data.user.lname);
        row.children(`td:nth-child(${td.email})`).html(data.user.email);

        row.children(`td:nth-child(${td.role})`).html(userRole(data.user.roles));
    }

    let insertRow = function(data){
        let row = `<tr>
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
        $("#myTable").prepend(row);
    }

    return {
        updateRow: updateRow,
        insertRow: insertRow,
    };
})();