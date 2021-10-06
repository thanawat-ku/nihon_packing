$(function () {
    $('#my-data-table').DataTable();
});

function deleteSplitLabel(event) {
    let splitLabel = event.currentTarget.name;
    console.log(splitLabel);
    var obj = JSON.parse(splitLabel);
    $("#deleteLotID").val(obj.id);
    $("#deleteLotNoLabelStatus").val(obj.status);
}

function addDefectLot(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#defectLotID").val(obj.id);
}

$(document).on(
    "click",
    " #deleteBt,#registerBt,#addDefectBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {

            case "deleteBt":
                deleteSplitLabel(event);
                break;
            case "registerBt":
                registerLabel(event);
                break;
            case "addDefectBt":
                addDefectLot(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);