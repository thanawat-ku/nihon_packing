$(function() {
    $('#my-data-table').DataTable();
});
function addSelectLabelForSell(event){
    let label = event.currentTarget.name;
    console.log(label);
    var obj = JSON.parse(label);
    $("#addLabelID").val(obj.id);
    $("#addLabelNo").text(obj.label_no);
}

  function cancelSelectLabelForSell(event){
    let label = event.currentTarget.name;
    console.log(label);
    var obj = JSON.parse(label);
    $("#cancelLabelID").val(obj.id);
    $("#cancelLabelNo").text(obj.label_no);
}

$(document).on(
    "click",
    "#addBt, #canBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "addBt":
                addSelectLabelForSell(event);
                break;
            case "canBt":
                cancelSelectLabelForSell(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);