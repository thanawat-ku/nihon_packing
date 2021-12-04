$(function () {
    $('#my-data-table').DataTable();
});
function addSelectLabelForSell(event) {
    let label = event.currentTarget.name;
    console.log(label);
    var obj = JSON.parse(label);
    $("#addLabelID").val(obj.id);
    $("#addLabelNo").text(obj.label_no);
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