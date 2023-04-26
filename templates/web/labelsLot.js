import DataTable from 'datatables.net-bs4';
$(function () {
    let table=new DataTable('#my-data-table',{
        "scrollX": true
    });
});
function editLabels(event) {
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#editLabelID").val(obj.id);
    $("#editLabelNo").val(obj.label_no);
    $("#editLotId").val(obj.lot_id);
    $("#editQuantity").val(obj.quantity);
    $("#editLabelType").val(obj.label_type);
    $("#editStatus").val(obj.status);
}

function deleteLabels(event) {
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#deleteLabelID").val(obj.id);
    $("#deleteLotNo").text(obj.label_no);
}


function voidLabel(event) {
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#voidLabelID").val(obj.id);
}

function splitLabel(event) {
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#SPLabelID").val(obj.id);
    $("#SPLabelNo").text(obj.label_no);
    $("#confirmSplitLabelNo").text(obj.label_no);
    $("#QtySystem").text(obj.quantity);
    $("#QtySystem2").text(obj.quantity);
}


function confirmSplit(event) {
    $("#confirmSPLabelID").val($("#SPLabelID").val());
    $("#qtyConfirm1").val($("#qty1").val());
    $("#qtyConfirm2").val($("#qty2").val());
    $("#confirmSplitQty1").text($("#qty1").val());
    $("#confirmSplitQty2").text($("#qty2").val());
    $("#addPrinterID2").val($("#addPrinterID").val());
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
    "#editBt, #deleteBt, #SplitBt ,#voidBt ,#confirmSplitBt ,#registerBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editLabels(event);
                break;
            case "deleteBt":
                deleteLabels(event);
                break;
            case "SplitBt":
                splitLabel(event);
                break;
            case "voidBt":
                voidLabel(event);
                break;
            case "confirmSplitBt":
                confirmSplit(event);
                break;
            case "registerBt":
                registerLot(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);