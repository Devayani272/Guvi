$(document).ready(function() {
localStorage.removeItem("loginSuccess");
  // Signup Submission Handling
  $("#signup-submit").on("click", function(e) {
    e.preventDefault();
    var name = $("#signup-name").val();
    var email = $("#signup-email").val();
    var password = $("#signup-password").val();
    $.ajax({
      url: "php/register.php",
      type: "POST",
      dataType: "json",
      data: { name: name, email: email, password: password },
      success: function(data) {
        if (data.code.name == 0) {
          $("#nameErr").remove();
        } else if (data.code.name == 1) {
          $("#nameErr").html("Name is required");
        }
        if (data.code.email == 0) {
          $("#emailErr").remove();
        } else if (data.code.email == 1) {
          $("#emailErr").html("Email is required");
        } else if (data.code.email == 2) {
          $("#emailErr").html("Invalid Email");
        } else if (data.code.email == 3) {
          $("#emailErr").html("Email already exists");
        }
        if (data.code.password == 0) {
          $("#passwordHelp").remove();
          $("#passwordErr").remove();
        } else if (data.code.password == 1) {
          $("#passwordHelp").remove();
          $("#passwordErr").html("Password is required");
        } else if (data.code.password == 2) {
          $("#passwordHelp").remove();
          $("#passwordErr").html("Password should atleast be 6 characters");
        }
        if (data.result == "success") {
          $("form").remove();
          $("#success")
            .delay(200)
            .addClass("pop_success");
        }
      }
    });
    return false;
  });
});
