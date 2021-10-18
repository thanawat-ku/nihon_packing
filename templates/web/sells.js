$(function() {
    $('#my-data-table').DataTable();
});
function editSell(event){
    let sell = event.currentTarget.name;
    console.log(sell);
    var obj = JSON.parse(sell);
    $("#editSellID").val(obj.id);
    $("#editSell_code").val(obj.part_code);
    $("#editSell_name").val(obj.part_name);
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