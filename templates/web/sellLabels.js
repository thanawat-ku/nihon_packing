$(function () {
    $('#my-data-table').DataTable();
});

function removeSellLabels(event) {
    let selllabel = event.currentTarget.name;
    console.log(selllabel);
    var obj = JSON.parse(selllabel);
    $("#removeLabelID").val(obj.id);
    $("#removeLabelNo").text(obj.label_no);
    
}

$(document).on(
    "click",
    "#removeBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "removeBt":
                removeSellLabels(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);