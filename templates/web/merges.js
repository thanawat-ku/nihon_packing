$(function() { //dont finish!!!!!
    $('#my-data-table').DataTable();
});
$( "#form-editUser" ).on("submit", function( event ) {
    if ( $( "#editPassword" ).val() ==  $( "#editConfirmPassword" ).val()) {
        if($("#editPassword").val()==""){
            $("#editUser").modal('toggle');
            $("#editPassword").remove();
        }
        return;
    }else{
        alert("Password not match");
        event.preventDefault();
    }
  }
);
function editUser(event){
    let user = event.currentTarget.name;
    console.log(user);
    var obj = JSON.parse(user);
}
function mergeDetail(event){
    let merge = event.currentTarget.name;
    console.log(merge);
    var obj = JSON.parse(merge);
    $("#detail_mergeID").val(obj.id);
    $("#detail_merge_NO").val(obj.merge_no);
    $("#detail_mergeQTY").val(obj.quantity);
}
$(document).on(
    "click",
    "#detailBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "detailBt":
                mergeDetail(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);