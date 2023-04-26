import DataTable from 'datatables.net-bs4';
$(function () {
    let table=new DataTable("#my-data-table",{ order: [[0, "desc"]] });
    $("#searchIssueStartDate, #searchIssueEndDate").datepicker({
        format: "yyyy-mm-dd",
    });
});

function removeLabel(event) {
    let label = event.currentTarget.name;
    console.log(label);
    var obj = JSON.parse(label);
    $("#removeLabelID").val(obj.label_id);
    $("#removeLabelNo").text(obj.label_no);
    $("#removeQuantity").text(obj.quantity);
}

function printLot(event) {
    let lotRow = event.currentTarget.name;
    console.log(lotRow);
    var obj = JSON.parse(lotRow);
    $("#printLotID").val(obj.id);
    $("#printLotNo").text(obj.lot_no);
    $("#realQTY").val(obj.quantity);
    $("#qtySystem").text(obj.quantity);
    $("#stdPack").val(obj.std_pack);
    $("#printLabelQty").text(obj.quantity_item);
    $("#printLNFP").text(obj.lNFPQty);
    
}

function confirmPrintLot(event) {
    $("#confirmPrintLotID").val($("#printLotID").val());
    $("#confirmRealQty").text($("#realQTY").val());
    $("#confirmRealQty2").val($("#realQTY").val());
    $("#addPrinterID2").val($("#addPrinterID").val());
    let real_qty = parseInt($("#realQTY").val());
    let std_pack = parseInt($("#stdPack").val());
    let num_packs = Math.ceil(real_qty / std_pack);
    let num_full_packs = Math.floor(real_qty / std_pack);
    let nonFully = 0;
    if (num_full_packs != num_packs) {
        nonFully = 1;
    }
    let num_pack_total = num_full_packs + nonFully;
    $("#numLabel").text(num_pack_total);
    $("#numLabelFully").text(num_full_packs);
    $("#numLabelNonFully").text(nonFully);

}

$(document).on("click", "#removeBt, #printBt, #confirmPrintLotBt", (event) => {
    let id = event.currentTarget.id;
    switch (id) {
        case "removeBt":
            removeLabel(event);
            break;
        case "printBt":
            printLot(event);
            break;
        case "confirmPrintLotBt":
            confirmPrintLot(event);
            break;
        default:
            console.log("no any events click");
    }
});
