$(function() {
    $('#my-data-table').DataTable();
});
function editCustomer(event){
    let customer = event.currentTarget.name;
    console.log(customer);
    var obj = JSON.parse(customer);
    $("#editLabelVoidReason").val(obj.id);
    $("#editReasonName").val(obj.reason_name);
    $("#editDescription").val(obj.description);
}

  function deleteCustomer(event){
    let customer = event.currentTarget.name;
    console.log(customer);
    var obj = JSON.parse(customer);
    $("#deleteVoidReasonId").val(obj.id);
    $("#deleteVoidReasonName").text(obj.reason_name);
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