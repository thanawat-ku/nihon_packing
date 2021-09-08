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

$(document).on(
    "click",
    "#editBt, #deleteBt, #printBt, #labelLotBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            
            case "deleteBt":
                deleteSplitLabel(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);