$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});

function addSellCpoItem(event) {
    let cpo = event.currentTarget.name;
    console.log(cpo);
    var obj = JSON.parse(cpo);
    $("#addCpoNo").text(obj.CpoNo);
    $("#addCpoItemID").val(obj.CpoItemID);
    $("#addRemainQty").val(obj.Quantity - obj.PackingQty);
    $("#addRemainMax").attr({ 'val': obj.Quantity - obj.PackingQty, "max": obj.Quantity - obj.PackingQty });
    $("#addRemainMax").val(obj.Quantity - obj.PackingQty);
}

function detailCpoItem(event) {
    let cpo = event.currentTarget.name;
    console.log(cpo);
    var obj = JSON.parse(cpo);
    $("#detailSellID").val(obj.sell_id);
    $("#detailCpoNo").text(obj.CpoNo);
    $("#detailCpoItemID").val(obj.CpoItemID);
    $("#detailRemainQty").val(obj.remain_qyt);
    $("#detailSellQty").val(obj.sell_qty);

}

$(document).on(
    "click",
    "#addBt, #detailBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "addBt":
                addSellCpoItem(event);
                break;
            case "detailBt":
                detailCpoItem(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);