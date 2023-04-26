import DataTable from 'datatables.net-bs4';
$(function () {
    let table=new DataTable('#my-data-table');
});
function addSelectLabelForPack(event) {
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
                addSelectLabelForPack(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);