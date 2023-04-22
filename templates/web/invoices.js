import DataTable from 'datatables.net-bs4';
$(function () {
    let table=new DataTable('#my-data-table',{ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});