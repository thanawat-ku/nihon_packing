$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});

function deleteScrapDetail(event){
    let scrap_detail = event.currentTarget.name;
    console.log(scrap_detail);
    var obj = JSON.parse(scrap_detail);
    $("#deleteScrapDetailID").val(obj.id);
    
}

$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editScrapDetail(event);
                break;
            case "deleteBt":
                deleteScrapDetail(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);