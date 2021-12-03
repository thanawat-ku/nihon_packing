$(function () {
    $('#my-data-table').DataTable();
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

function splitLabel(event) {
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#SPLabelID").val(obj.id);
    $("#SPLabelNo").text(obj.label_no);
}

function voidLabel(event) {
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#voidLabelID").val(obj.id);
}

function registerMerge(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#registerMergeId").val(obj.id);
    $("#registerMergeNo").text(obj.merge_no);

}

$(document).on(
    "click",
    "#editBt, #deleteBt, #SplitBt ,#voidBt ,#registerBt",
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
            case "registerBt":
                registerMerge(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);