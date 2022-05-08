$(function () {
    $('#my-data-table').DataTable();
});
function editCpoItem(event) {
    let cpo = event.currentTarget.name;
    console.log(cpo);
    var obj = JSON.parse(cpo);
    $("#editCpoItemID").val(obj.id);
    $("#editCpoItemIDText").text(obj.cpo_item_id);
    $("#editPackQty").val(obj.pack_qty);
    $("#editPackQtyMax").attr({"max":obj.quantity-obj.packing_qty});
    $("#editPackQtyMax").val(obj.pack_qty);
}

function deleteCpoItem(event) {
    let cpo = event.currentTarget.name;
    console.log(cpo);
    var obj = JSON.parse(cpo);
    $("#deletePackCpoItemID").val(obj.id);
    $("#deletePackQty").text(obj.pack_qty);
    $("#deleteCpoItemID").text(obj.cpo_item_id);
}

function comfirmCpoItem(event) {
    let sellRow = event.currentTarget.name;
    console.log(sellRow);
    var obj = JSON.parse(sellRow);
    $("#conPackID").val(obj.pack_id);
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