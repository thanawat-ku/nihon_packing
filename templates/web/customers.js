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