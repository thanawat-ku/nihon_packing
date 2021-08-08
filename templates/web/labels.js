$(function() {
    $('#my-data-table').DataTable();
});
function editLabels(event){
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

  function deleteLabels(event){
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#deleteLabelID").val(obj.id);
    $("#deleteLabelNo").text(obj.label_no);
}
$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editLabels(event);
                break;
            case "deleteBt":
                deleteLabels(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);