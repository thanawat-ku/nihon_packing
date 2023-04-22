import DataTable from 'datatables.net-bs4';
$(function () {
    let table=new DataTable('#my-data-table');
});

function removePackLabels(event) {
    let packLabel = event.currentTarget.name;
    console.log(packLabel);
    var obj = JSON.parse(packLabel);
    $("#removeLabelID").val(obj.id);
    $("#removeLabelNo").text(obj.label_no);
    $("#removeQuantity").text(obj.quantity);   
}

$(document).on(
    "click",
    "#removeBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "removeBt":
                removePackLabels(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);