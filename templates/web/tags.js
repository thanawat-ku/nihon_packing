import DataTable from 'datatables.net-bs4';
$(function () {
    let table=new DataTable('#my-data-table',{ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});

$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editPack(event);
                break;
            case "deleteBt":
                deletePack(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);