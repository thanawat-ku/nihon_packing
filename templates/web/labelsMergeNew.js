$(function () { //dont finish!!!!!
    $('#my-data-table').DataTable();
});

function splitLabel(event) {
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#SPLabelID").val(obj.id);
    $("#SPLabelNo").text(obj.label_no);
}

function voidLabel(event) {
    let labels = event.currentTarget.name;
    console.log(labels);
    var obj = JSON.parse(labels);
    $("#voidLabelID").val(obj.id);
}

$(document).on(
    "click",
    "#deleteDetailBt ,#SplitBt,#voidBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "deleteDetailBt":
                deleteDetail(event);
                break;
            case "SplitBt":
                splitLabel(event);
                break;
            case "voidBt":
                voidLabel(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);