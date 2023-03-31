var flag=0;
$(document).ready(function() {  
  //Index Page Handling
  if ($("#index-form").length == 1) {
    $.ajax({
      url: "php/profile.php",
      dataType: "json",
      type: "GET",
      success: function(data) {
        $("#profile-name").val(data.details.name);
        $("#profile-email").val(data.details.email);
        $("#profile-age").val(data.details.age);
        $("#profile-dob").val(data.details.dob);
        data.details.contact == 0
          ? $("#profile-contact").val("")
          : $("#profile-contact").val(data.details.contact);
      }
    });
    $("#profile-submit").on("click", function(e) {
      e.preventDefault();
      var name = $("#profile-name").val();
      var dob = $("#profile-dob").val();
      var contact = $("#profile-contact").val();
      $.ajax({
        url: "php/profile.php",
        type: "POST",
        dataType: "json",
        data: { name: name, dob: dob, contact: contact },
        success: function() {
          location.reload(true);
        }
      });
      return false;
    });
  }
});
function logout() {
  console.log("logout");
  localStorage.removeItem("loginSuccess");
}; 
