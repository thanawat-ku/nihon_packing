$(function() {
    $('#my-data-table').DataTable();
});
function editLabels(event){
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#editLabelsID").val(obj.id);
    $("#editLabelsName").val(obj.labels_name);
    $("#editTelNo").val(obj.tel_no);
    $("#editAddress").val(obj.address);
}

  function deleteLabels(event){
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#deleteLabelsID").val(obj.id);
    $("#deleteLabelsName").text(obj.labels_name);
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