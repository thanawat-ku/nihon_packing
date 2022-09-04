$(function() {
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
    $("#PartID-report").val(0);
    $("#ModelID-report").val(0);
    $("#StoreID-report").val(0);
    $("#MachineID-report").val(0);
});