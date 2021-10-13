$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});
// $(function () {
//     $('#my-data-table').DataTable();
// });
function editLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#editLotID").val(obj.id);
    $("#editLotNo").val(obj.lot_no);
    $("#editProductID").val(obj.product_id);
    $("#editQuantity").val(obj.quantity);
}

function deleteLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#deleteLotID").val(obj.id);
    $("#deleteLotNoLabelStatus").val(obj.status);
}
function printLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#printLotID").val(obj.id);
    $("#printLotNo").text(obj.lot_no);

}

function confirmPrintLot(event) {
    $("#confirmPrintLotID").val($("#printLotID").val());
    $("#confirmRealQty").text($("#realQTY").val());
    $("#confirmRealQty2").val($("#realQTY").val());
}

function addDefectLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#defectLotID").val(obj.id);
}

function registerLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#registerLotID").val(obj.id);
    $("#registerLotNo").text(obj.generate_lot_no);

}

$(document).on(
    "click",
    "#editBt, #deleteBt, #printBt, #addDefectBt, #registerBt, #confirmPrintLotBt",
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
            case "addDefectBt":
                addDefectLot(event);
                break;
            case "registerBt":
                registerLot(event);
                break;
            case "confirmPrintLotBt":
                confirmPrintLot(event)
                break;

            default:
                console.log("no any events click");
        }
    }
);