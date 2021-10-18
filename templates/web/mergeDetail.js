$(function () { //dont finish!!!!!
    $('#my-data-table').DataTable();
});


function deleteDetail(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#deleteLabelID").val(obj.id);
    $("#deleteMergeID").val(obj.from_merge_id);
    $("#deleteLabelNo").text(obj.label_no);
}

$(document).on(
    "click",
    "#deleteDetailBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "deleteDetailBt":
                deleteDetail(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);