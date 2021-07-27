$(function() {
    $('#my-data-table').DataTable();
});
function editCustomer(event){
    let customer = event.currentTarget.name;
    console.log(customer);
    var obj = JSON.parse(customer);
    $("#editCustomerID").val(obj.id);
    $("#editeditCustomerName").val(obj.customer_name);
    $("#editeditTelNo").val(obj.tel_no);
    $("#editeditAddress").val(obj.address);
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
$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editLot(event);
                break;
            case "deleteBt":
                deleteLot(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);