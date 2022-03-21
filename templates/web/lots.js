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
    $("#realQTY").val(obj.quantity);
    $("#qtySystem").text(obj.quantity);


}

function confirmPrintLot(event) {
    $("#confirmPrintLotID").val($("#printLotID").val());
    $("#confirmRealQty").text($("#realQTY").val());
    $("#confirmRealQty2").val($("#realQTY").val());
    $("#addPrinterID2").val($("#addPrinterID").val());
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
        }
    });
}
$(document).on(
    "click",
    "#syncBt",
    (event) => {
        syncCustomers();
    }
);
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