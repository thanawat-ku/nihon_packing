$(function() {
    $('#my-data-table').DataTable();
});
function editProduct(event){
<<<<<<< HEAD
    let product = event.currentTarget.name;
    console.log(product);
    var obj = JSON.parse(product);
    $("#editProductID").val(obj.id);
    $("#editProduct_code").val(obj.product_code);
    $("#editProduct_name").val(obj.product_name);
=======
    let user = event.currentTarget.name;
    console.log(user);
    var obj = JSON.parse(user);
    $("#editProductID").val(obj.id);
    $("#editProductCode").val(obj.product_code);
    $("#editProductName").val(obj.product_name);
>>>>>>> tae
    $("#editPrice").val(obj.price);
    $("#editStdPack").val(obj.std_pack);
    $("#editStdBox").val(obj.std_box);
}
<<<<<<< HEAD

  function deleteProduct(event){
    let product = event.currentTarget.name;
    console.log(product);
    var obj = JSON.parse(product);
    $("#deleteProductID").val(obj.id);
    $("#deleteProductCode").text(obj.product_code);
    $("#deleteProductName").text(obj.product_name);
}

=======
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
>>>>>>> tae
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