$(window).on('load', function () {
    // activate sliders (if there are any)
    $("#showAddressForm").click(function () {
        $("#form-flat").fadeOut(200, "linear", function () {
            $("#form-address").fadeIn(200, "linear");
        });
    });
    $("#hideAddressForm").click(function () {
        $("#form-address").fadeOut(200, "linear", function () {
            $("#form-flat").fadeIn(200, "linear");
        });
    });
    $("#saveAddress").click(function () {
    });
});