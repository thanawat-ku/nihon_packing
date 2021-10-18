$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});

function selectLabel(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#selectLabelId").val(obj.id);
    $("#mergePackId").val(obj.from_merge_id);
    $("#selectLabelNo").text(obj.label_no);
}

$(document).on(
    "click",
    "#selectBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "selectBt":
                selectLabel(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);