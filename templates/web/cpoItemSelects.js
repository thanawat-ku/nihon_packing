$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});

function addPackCpoItem(event) {
    let cpo = event.currentTarget.name;
    console.log(cpo);
    var obj = JSON.parse(cpo);
    $("#addCpoItemIDText").text(obj.CpoItemID);
    $("#addCpoItemID").val(obj.CpoItemID);
    $("#addRemainQty").val(obj.Quantity - obj.PackingQty);
    $("#addRemainMax").attr({ 'val': obj.Quantity - obj.PackingQty, "max": obj.Quantity - obj.PackingQty });
    $("#addRemainMax").val(obj.Quantity - obj.PackingQty);
}

function detailCpoItem(event) {
    let cpo = event.currentTarget.name;
    console.log(cpo);
    var obj = JSON.parse(cpo);
    $("#detailPackIDText").val(obj.pack_id);
    $("#detailCpoItemID").text(obj.CpoItemID);
    $("#detailCpoItemID").val(obj.CpoItemID);
    $("#detailRemainQty").val(obj.remain_qyt);
    $("#detailPackQty").val(obj.pack_qty);

}

$(document).on(
    "click",
    "#addBt, #detailBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "addBt":
                addPackCpoItem(event);
                break;
            case "detailBt":
                detailCpoItem(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);