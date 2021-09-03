$(function() {
    $('#my-data-table').DataTable();
});
// function editUser(event){
//     let user = event.currentTarget.name;
//     console.log(user);
//     var obj = JSON.parse(user);
//     $("#editUserID").val(obj.id);
//     $("#editUserName").val(obj.username);
//     $("#editEmail").val(obj.email);
//     $("#editFirstName").val(obj.first_name);
//     $("#editLastName").val(obj.last_name);
//     $("#editUserRoleID").val(obj.user_role_id);
//     $("#editStoreID").val(obj.store_id);
//     $("#editEnabled").val(obj.enabled);
// }
$( "#form-editUser" ).on("submit", function( event ) {
    if ( $( "#editPassword" ).val() ==  $( "#editConfirmPassword" ).val()) {
        if($("#editPassword").val()==""){
            $("#editUser").modal('toggle');
            $("#editPassword").remove();
        }
        return;
    }else{
        alert("Password not match");
        event.preventDefault();
    }
  });
$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editUser(event);
                break;
            case "deleteBt":
                deleteUser(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);