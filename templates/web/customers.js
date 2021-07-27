$(function() {
    $('#my-data-table').DataTable();
});
function editCustomer(event){
    let customer = event.currentTarget.name;
    console.log(customer);
    var obj = JSON.parse(customer);
    $("#editCustomerID").val(obj.id);
    $("#editCustomerName").val(obj.customer_name);
    $("#editTelNo").val(obj.tel_no);
    $("#editAddress").val(obj.address);
}
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
  function deleteCustomer(event){
    let customer = event.currentTarget.name;
    console.log(customer);
    var obj = JSON.parse(customer);
    $("#deleteCustomerID").val(obj.id);
    $("#deleteCustomerName").text(obj.customer_name);
}
$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editCustomer(event);
                break;
            case "deleteBt":
                deleteCustomer(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);