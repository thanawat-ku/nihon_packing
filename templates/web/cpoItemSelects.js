$(function () {
    $('#my-data-table').DataTable();
});
function addSellCpoItem(event) {
    let cpo = event.currentTarget.name;
    console.log(cpo);
    var obj = JSON.parse(cpo);
    $("#addCpoNo").text(obj.CpoNo);
    $("#addCpoItemID").val(obj.CpoItemID);
    $("#addRemainQty").val(obj.Quantity - obj.PackingQty);
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

function gg(event) {
    let sellRow = event.currentTarget.name;
    console.log(sellRow);
    var obj = JSON.parse(sellRow);
    $("#ggSellID").text(obj.id);
    // $("#detailCpoNo").text(obj.CpoNo);
    // $("#detailCpoItemID").val(obj.CpoItemID);
    $("#ggRemainQty").val(obj.remain_qyt);
    $("#ggSellQty").val(obj.sell_qty);
}
$(document).on(
    "click",
    "#addBt, #detailBt, #gg",
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