import DataTable from 'datatables.net-bs4';
$(function() {
    let table=new DataTable('#my-data-table');
});
function editCustomer(event){
    let customer = event.currentTarget.name;
    console.log(customer);
    var obj = JSON.parse(customer);
    $("#editCustomerID").val(obj.id);
    $("#editCustomerCode").val(obj.customer_code);
    $("#editCustomerName").val(obj.customer_name);
    $("#editIsSync").val(obj.is_sync);
}

  function deleteCustomer(event){
    let customer = event.currentTarget.name;
    console.log(customer);
    var obj = JSON.parse(customer);
    $("#deleteCustomerID").val(obj.id);
    $("#deleteCustomerCode").text(obj.customer_code);
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