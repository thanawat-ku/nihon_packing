$(function () {
    $('#my-data-table').DataTable();
});


function addDefectLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#defectLotID").val(obj.id);
}

function editDefectLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#lotDefectID").val(obj.id);
    $("#editDefectId").val(obj.defect_id);
    $("#editQuantity").val(obj.quantity); 
    $("#editQuantityMax").attr({"max":obj.quantity});
    $("#editdefectLotID").val(obj.lot_id);
}

function deleteDefectLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#deleteLotDefectID").val(obj.id);
    $("#deleteDefectLotID").val(obj.lot_id);
    $("#deleteDefectCode").text(obj.defect_code);
}

$(document).on(
    "click",
    " #deleteDefectBt,#addDefectBt,#editDefectBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "deleteDefectBt":
                deleteDefectLot(event);
                break;
            case "addDefectBt":
                addDefectLot(event);
                break;
            case "editDefectBt":
                editDefectLot(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);