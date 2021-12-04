// $(function () {
//     $('#my-data-table').DataTable();
// });

$(function() {
    $('#my-data-table').DataTable({"order": [[ 0, "desc" ]]});
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});

function deleteSplitLabel(event) {
    let splitLabel = event.currentTarget.name;
    console.log(splitLabel);
    var obj = JSON.parse(splitLabel);
    $("#deleteSplitLabelID").val(obj.id);
    $("#deleteSplitLabelNoLabelId").val(obj.label_id);
    $("#deleteSplitNo").text(obj.split_label_no);
}

// function registerLabel(event) {
//     let lot = event.currentTarget.name;
//     console.log(lot);
//     var obj = JSON.parse(lot);
//     $("#registerSpId").val(obj.id);
//     $("#registerSpNo").text(obj.split_label_no);

// }

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