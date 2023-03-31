$(document).ready(function () {
  //Login Submission Handling
  $("#login-submit").on("click", function (e) {

    e.preventDefault();
    var email = $("#login-email").val();
    var password = $("#login-password").val();
    $.ajax({
      url: "php/login.php",
      type: "POST",
      dataType: "json",
      data: { email: email, password: password },
      success: function (data) {
        if (data.code.email == 0) {
          $("#emailErr").remove();
        } else if (data.code.email == 1) {
          $("#emailErr").html("Email is required");
        } else if (data.code.email == 2) {
          $("#emailErr").html("Invalid Email");
        }
        if (data.code.password == 0) {
          $("#passwordErr").remove();
        } else if (data.code.password == 1) {
          $("#passwordErr").html("Password is required");
        } else if (data.code.email == 3 && data.code.password == 2) {
          $("#emailErr").remove();
          $("#passwordErr").html("Email Id and/or password is/are incorrect");
        }
        if (data.result == "success") {

          window.location.href = window.location.href.substr(
            0,
            window.location.href.lastIndexOf("/")
          );
          localStorage.setItem("loginSuccess", "true");
        }
      }
    });
    return false;
  });
});
