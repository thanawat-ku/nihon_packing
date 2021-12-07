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
    $("#editScrapDate").val(obj.scrap_date);

}

function deleteScrap(event) {
    let scrap = event.currentTarget.name;
    console.log(scrap);
    var obj = JSON.parse(scrap);
    $("#deleteScrapID").val(obj.id);
    $("#deleteScrapNo").text(obj.scrap_no);

}

function rejectScrap(event) {
    let scrap = event.currentTarget.name;
    console.log(scrap);
    var obj = JSON.parse(scrap);
    $("#rejectScrapID").val(obj.id);
    $("#rejectScrapNo").text(obj.scrap_no);

}

function acceptScrap(event) {
    let scrap = event.currentTarget.name;
    console.log(scrap);
    var obj = JSON.parse(scrap);
    $("#acceptScrapID").val(obj.id);
    $("#acceptScrapNo").text(obj.scrap_no);

}

function confirmScrap(event) {
    let scrap = event.currentTarget.name;
    console.log(scrap);
    var obj = JSON.parse(scrap);
    $("#conScrapID").val(obj.id);
    $("#conScrapNo").text(obj.scrap_no);

}

$(document).on(
    "click",
    "#editBt, #deleteBt, #rejectBt, #acceptBt, #conBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editScrap(event);
                break;
            case "deleteBt":
                deleteScrap(event);
                break;
            case "rejectBt":
                rejectScrap(event);
                break;
            case "acceptBt":
                acceptScrap(event);
                break;
            case "conBt":
                confirmScrap(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);