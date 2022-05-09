$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});
function editPack(event) {
    let pack = event.currentTarget.name;
    console.log(pack);
    var obj = JSON.parse(pack);
    $("#editPackID").val(obj.id);
    $("#editPackNo").text(obj.pack_no);
    $("#editPackStatus").val(obj.pack_status);

}

function deletePack(event) {
    let pack = event.currentTarget.name;
    console.log(pack);
    var obj = JSON.parse(pack);
    $("#deletePackID").val(obj.id);
    $("#deletePackNo").text(obj.pack_no);

}

function registerTag(event) {
    let pack = event.currentTarget.name;
    console.log(pack);
    var obj = JSON.parse(pack);
    $("#regisPackID").val(obj.id);
    $("#regisPackNo").text(obj.pack_no);

}

function confirmPack(event) {
    let pack = event.currentTarget.name;
    console.log(pack);
    var obj = JSON.parse(pack);
    $("#conPackID").val(obj.id);
    $("#conPackNo").text(obj.pack_no);

}

function printTags(event) {
    let pack = event.currentTarget.name;
    console.log(pack);
    var obj = JSON.parse(pack);
    $("#printPackID").val(obj.id);
    $("#printPackNo").text(obj.pack_no);

}

$(document).on(
    "click",
    "#editBt, #deleteBt, #registerBt, #conBt, #printBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editPack(event);
                break;
            case "deleteBt":
                deletePack(event);
                break;
            case "registerBt":
                registerTag(event);
            case "conBt":
                confirmPack(event);
                break;
            case "printBt":
                printTags(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);