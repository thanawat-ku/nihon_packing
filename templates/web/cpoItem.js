$(function () {
    $('#my-data-table').DataTable();
});
function editCpoItem(event) {
    let sell = event.currentTarget.name;
    console.log(sell);
    var obj = JSON.parse(sell);
    $("#editCpoItemID").val(obj.id);
    $("#editCpoNo").text(obj.cpo_no);
    $("#editSellQty").val(obj.sell_qty);
    $("#editSellQtyMax").attr({"max":obj.quantity-obj.packing_qty});
    $("#editSellQtyMax").val(obj.sell_qty);
}

function deleteCpoItem(event) {
    let sell = event.currentTarget.name;
    console.log(sell);
    var obj = JSON.parse(sell);
    $("#deleteSellCpoItemID").val(obj.id);
    $("#deleteSellQty").text(obj.sell_qty);
    $("#deleteCpoNo").text(obj.cpo_no);
}

function comfirmCpoItem(event) {
    let sellRow = event.currentTarget.name;
    console.log(sellRow);
    var obj = JSON.parse(sellRow);
    $("#conSellID").val(obj.sell_id);
}

$(document).on(
    "click",
    "#editBt, #deleteBt, #conBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editCpoItem(event);
                break;
            case "deleteBt":
                deleteCpoItem(event);
                break;
            case "conBt":
                comfirmCpoItem(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);