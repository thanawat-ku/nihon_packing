import DataTable from 'datatables.net-bs4';
$(function () {
    let table=new DataTable('#my-data-table',{ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});

function selectLabel(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#selectLabelId").val(obj.id);
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