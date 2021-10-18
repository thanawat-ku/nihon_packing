$(function () {
    $('#my-data-table').DataTable();
});
function editCpoItem(event) {
    let sell = event.currentTarget.name;
    console.log(sell);
    var obj = JSON.parse(sell);
    $("#editCpoItemID").val(obj.id);
    $("#editCpoItem_code").val(obj.part_code);
    $("#editCpoItem_name").val(obj.part_name);
    $("#editStdPack").val(obj.std_pack);
    $("#editStdBox").val(obj.std_box);
}

function deleteCpoItem(event) {
    let sell = event.currentTarget.name;
    console.log(sell);
    var obj = JSON.parse(sell);
    $("#deleteCpoItemID").val(obj.id);
    $("#deleteCpoItemCode").text(obj.part_code);
    $("#deleteCpoItemName").text(obj.part_name);
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