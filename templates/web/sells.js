$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});
function editSell(event){
    let sell = event.currentTarget.name;
    console.log(sell);
    var obj = JSON.parse(sell);
    $("#editSellID").val(obj.id);
    $("#editSellNo").text(obj.sell_no);
    $("#editSellStatus").val(obj.sell_status);
    
}

function deleteSell(event){
    let sell = event.currentTarget.name;
    console.log(sell);
    var obj = JSON.parse(sell);
    $("#deleteSellID").val(obj.id);
    $("#deleteSellNo").text(obj.sell_no);
    
}

$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editSell(event);
                break;
            case "deleteBt":
                deleteSell(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);