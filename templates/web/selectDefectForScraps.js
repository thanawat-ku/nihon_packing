$(function () {
    $('#my-data-table').DataTable();
});
function addSelectLabelForSell(event) {
    let lotDefect = event.currentTarget.name;
    console.log(lotDefect);
    var obj = JSON.parse(lotDefect);
    $("#addLotDefectID").val(obj.id);
    $("#addDefectCode").text(obj.defect_code);
    $("#addQuantity").text(obj.quantity);
}

$(document).on(
    "click",
    "#addBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "addBt":
                addSelectLabelForSell(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);