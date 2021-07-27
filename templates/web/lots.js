$(function() {
    $('#my-data-table').DataTable();
});
function editLot(event){
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#editLotID").val(obj.id);
    $("#editLotNo").val(obj.lot_no);
    $("#editProductID").val(obj.product_id);
    $("#editQuantity").val(obj.quantity);
}
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
  });
  function deleteLot(event){
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#deleteLotID").val(obj.id);
    $("#deleteLotNo").text(obj.lot_no);
}
$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editLot(event);
                break;
            case "deleteBt":
                deleteLot(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);