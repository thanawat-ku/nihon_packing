$(function() {
    $('#my-data-table').DataTable();
    $('#, #seasearchIssueStartDaterchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});
function editSell(event){
    let sell = event.currentTarget.name;
    console.log(sell);
    var obj = JSON.parse(sell);
    $("#editSellID").val(obj.id);
    $("#editSellNo").text(obj.sell_no);
    $("#editProductCode").text(obj.part_code);
    $("#editProductName").text(obj.part_name);
    $("#editSellStatus").text(obj.sell_status);
    $("#editStdPack").val(obj.std_pack);
    $("#editStdBox").val(obj.std_box);
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