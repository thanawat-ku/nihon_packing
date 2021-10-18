$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});

function editMerge(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#editMergeID").val(obj.id);
    $("#editProductID").val(obj.product_id);
}

function deleteMerge(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#deleteMergeId").val(obj.id);
    $("#deleteMergeNo").text(obj.merge_no);
}

function confirmMerge(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#confirmMergeId").val(obj.id);
    $("#stdPack").val(obj.std_pack);
    $("#confirmMergeNo").text(obj.merge_no);
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
    "#editBt ,#deleteBt ,#confirmBt ,#registerBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editMerge(event);
                break;
            case "deleteBt":
                deleteMerge(event);
                break;
            case "confirmBt":
                confirmMerge(event);
                break;
            case "registerBt":
                registerMerge(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);