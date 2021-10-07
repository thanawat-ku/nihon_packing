$(function () {
    $('#my-data-table').DataTable();
});

function deleteSplitLabel(event) {
    let splitLabel = event.currentTarget.name;
    console.log(splitLabel);
    var obj = JSON.parse(splitLabel);
    $("#deleteLotID").val(obj.id);
    $("#deleteLotNoLabelStatus").val(obj.label_id);
}

function registerLabel(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#registerSpId").val(obj.id);
    $("#deleteSplitLabelNoLabelId").text(obj.split_label_no);

}

$(document).on(
    "click",
    " #deleteBt,#registerBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {

            case "deleteBt":
                deleteSplitLabel(event);
                break;
            case "registerBt":
                registerLabel(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);