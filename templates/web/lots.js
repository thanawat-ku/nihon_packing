$(function () {
    $('#my-data-table').DataTable({
        "order": [[0, "desc"]],
        "scrollX": true
    });
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
    $("#editProductID").selectpicker('val', obj.product_id);
    $("#editQuantity").val(obj.quantity);
}

function deleteLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#deleteLotID").val(obj.id);
    $("#deleteLotNo").text(obj.lot_no); //show lot_no
    $("#deleteLotNoLabelStatus").val(obj.status);
}
function printLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#printLotID").val(obj.id);
    $("#printLotNo").text(obj.lot_no);
    $("#realQty").val(Math.floor(obj.quantity) + Math.floor(obj.merge_qty));
    $("#realLotQty").val(obj.real_lot_qty)
    let realLotQty = parseInt($("#realLotQty").val());
    if(realLotQty == 0){
        $("#realLotQty").val(obj.quantity);
    }else{
        $("#realLotQty").val(obj.real_lot_qty);
    }
    $("#qtySystem").text(obj.quantity);
    $("#mergeQty").val(obj.merge_qty);
    $("#stdPack").val(obj.std_pack);


}
function reprintLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#reprintLotID").val(obj.id);
    $("#reprintLotNo").text(obj.lot_no);
}
function reverseLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#reverseLotID").val(obj.id);
    $("#reverseLotNo").text(obj.lot_no);
}

function confirmPrintLot(event) {
    $("#conrealLotQty").val($("#realLotQty").val());
    $("#conmergeQty").val($("#mergeQty").val());
    $("#confirmPrintLotID").val($("#printLotID").val());
    $("#confirmRealQty").text($("#realQty").val());
    $("#confirmRealQty2").val($("#realQty").val());
    $("#addPrinterID2").val($("#addPrinterID").val());
    let real_qty = parseInt($("#realQty").val());
    let std_pack = parseInt($("#stdPack").val());
    let num_packs = Math.ceil(real_qty / std_pack);
    let num_full_packs = Math.floor(real_qty / std_pack);
    let nonFully = 0;
    if(num_full_packs != num_packs){
        nonFully = 1;
    }
    let num_pack_total = num_full_packs + nonFully;
    $("#numLabel").text(num_pack_total);
    $("#numLabelFully").text(num_full_packs);
    $("#numLabelNonFully").text(nonFully);
    
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

function syncCustomers() {
    $('#syncTable').text("Customer start");
    $.ajax({
        type: "GET",
        url: "api/mis_sync_customers",
        success: function (data) {
            if (data.length > 0) {
                code = data[0].CustomerCode + ' ';
            } else {
                code = "";
            }
            // Append to the html
            $('#syncTable').text("Customer last code: " + code);
            syncProducts();
        }
    });
}
function syncProducts() {
    $('#syncTable').text("Product start");
    $.ajax({
        type: "GET",
        url: "api/mis_sync_products",
        success: function (data) {
            if (data.length > 0) {
                code = data[0].PartCode + ' ';
            } else {
                code = "";
            }
            $('#syncTable').text("Product last code: " + code);
            syncSections();
        }
    });
}

function syncSections() {
    $('#syncTable').text("Section start");
    $.ajax({
        type: "GET",
        url: "api/mis_sync_sections",
        success: function (data) {
            if (data.length > 0) {
                code = data[0].SectionName + ' ';
            } else {
                code = "";
            }
            $('#syncTable').text("Section last code: " + code);
            syncDefects();
        }
    });
}
function syncDefects() {
    $('#syncTable').text("Defect start");
    $.ajax({
        type: "GET",
        url: "api/mis_sync_defects",
        success: function (data) {
            if (data.length > 0) {
                code = data[0].CuaseName + ' ';
            } else {
                code = "";
            }
            $('#syncTable').text("Defect last code: " + code);
            syncLots();
        }
    });
}
function syncLots() {
    $('#syncTable').text("Lot start");
    $.ajax({
        type: "GET",
        url: "api/mis_sync_lots",
        success: function (data) {
            if (data.length > 0) {
                code = data[0].LotNo + ' ';
            } else {
                code = "";
            }
            // Append to the html
            $('#syncTable').text("lot last code: " + code);
            syncInvoices();
        }
    });
}

function syncInvoices() {
    $('#syncTable').text("Invoice start");
    $.ajax({
        type: "GET",
        url: "api/mis_sync_invoice",
        success: function (data) {
            if (data.length > 0) {
                code = data[0].invoice_no + ' ';
            } else {
                code = "";
            }
            // Append to the html
            $('#syncTable').text("invoice last no: " + code);
        },
        error: function(error) {
            console.log('Error: ' + error);
        }
    });
}
function changeRealLotQty() {
    $("#realQty").val(eval($("#realLotQty").val())+eval($("#mergeQty").val()))
}
$(document).on(
    "click",
    "#syncBt",
    (event) => {
        syncCustomers();
    }
);
$(document).on(
    "change",
    "#realLotQty",
    (event) => {
        changeRealLotQty();
    }
);
$(document).on(
    "click",
    "#editBt, #deleteBt, #printBt, #reprintBt, #reverseBt, #addDefectBt, #registerBt, #confirmPrintLotBt",
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
            case "reprintBt":
                reprintLot(event);
                break;
            case "reverseBt":
                reverseLot(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);