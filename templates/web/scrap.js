$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});
function editScrap(event) {
    let scrap = event.currentTarget.name;
    console.log(scrap);
    var obj = JSON.parse(scrap);
    $("#editScrapID").val(obj.id);
    $("#editScrapDate").datepicker({
        format: 'yyyy-mm-dd'
    }, 'val', obj.scrap_date);

}

function deleteScrap(event) {
    let scrap = event.currentTarget.name;
    console.log(scrap);
    var obj = JSON.parse(scrap);
    $("#deleteScrapID").val(obj.id);
    $("#deleteScrapNo").text(obj.scrap_no);

}

$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editScrap(event);
                break;
            case "deleteBt":
                deleteScrap(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);