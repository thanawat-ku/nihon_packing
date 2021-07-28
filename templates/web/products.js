$(function() {
    $('#my-data-table').DataTable();
});
function editProduct(event){
    let user = event.currentTarget.name;
    console.log(user);
    var obj = JSON.parse(user);
    $("#editProductID").val(obj.id);
    $("#editProductCode").val(obj.product_code);
    $("#editProductName").val(obj.product_name);
    $("#editPrice").val(obj.price);
    $("#editStdPack").val(obj.std_pack);
    $("#editStdBox").val(obj.std_box);
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
  function deleteProduct(event){
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#deleteProductID").val(obj.id);
    $("#deleteProductNo").text(obj.product_code);
}
$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editProduct(event);
                break;
            case "deleteBt":
                deleteProduct(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);