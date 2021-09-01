$(function () {
    $('#my-data-table').DataTable();
});
function editLot(event) {
    let splitLabel = event.currentTarget.name;
    console.log(splitLabel);
    var obj = JSON.parse(splitLabel);
    $("#editLotID").val(obj.id);
    $("#editLotNo").val(obj.splitLabel_no);
    $("#editProductID").val(obj.product_id);
    $("#editQuantity").val(obj.quantity);
}

function deleteLot(event) {
    let splitLabel = event.currentTarget.name;
    console.log(splitLabel);
    var obj = JSON.parse(splitLabel);
    $("#deleteLotID").val(obj.id);
    $("#deleteLotNoLabelStatus").val(obj.status);
}

$(document).on(
    "click",
    "#editBt, #deleteBt, #printBt, #labelLotBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editLot(event);
                break;
            case "deleteBt":
                deleteLot(event);
                break;
            case "printBt":
                printLot(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);