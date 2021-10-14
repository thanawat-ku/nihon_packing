$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});


$(document).on(
    "click",
    "#detailBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "detailBt":
                mergeDetail(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);