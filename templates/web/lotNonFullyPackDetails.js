$(function () {
    $("#my-data-table").DataTable({ order: [[0, "desc"]] });
    $("#searchIssueStartDate, #searchIssueEndDate").datepicker({
        format: "yyyy-mm-dd",
    });
});

function addSelectLabelMergeLot(event) {
    let label = event.currentTarget.name;
    console.log(label);
    var obj = JSON.parse(label);
    $("#addLabelID").val(obj.id);
    $("#addLabelNo").text(obj.label_no);
    $("#addQuantity").text(obj.quantity);
}

$(document).on("click", "#addBt", (event) => {
    let id = event.currentTarget.id;
    switch (id) {
        case "addBt":
            addSelectLabelMergeLot(event);
            break;
        default:
            console.log("no any events click");
    }
});
