const URL_PATH = "http://192.168.0.35";

function setDefaultTZ(value) {
  document.getElementById("timezone").value = value;
}

function toggle_register() {
  // Toggles the text/link "Register" between "Already registered" and "Register"
  // Also toggles the visibility of the Retype Password field, and adjusts the "Forgot Password?"
  // text/link's style appropriately.
  let confirm_password_label = document.getElementById(
    "confirm_password_label"
  );
  let confirm_password_field = document.getElementById(
    "confirm_password_field"
  );
  let register_link = document.getElementById("register");
  let forgot_pass_link = document.getElementById("forgot_pass");
  let login_button = document.getElementById("login_button");

  if (register_link.innerHTML === "Register") {
    register_link.innerHTML = "Already registered";
    forgot_pass_link.style = "padding-left: 10px;";
    login_button.value = "Sign Up";
  } else {
    register_link.innerHTML = "Register";
    forgot_pass_link.style = "padding-left: 45px;";
    confirm_password_field.value = ""; // Reset the field to empty if the user switches
    login_button.value = "Log in";
    // back to normal logging in
  }
  confirm_password_label.hidden = !confirm_password_label.hidden;
  confirm_password_field.hidden = !confirm_password_field.hidden;
}

function logout() {
  var xhttp = new XMLHttpRequest();
  xhttp.open("GET", "http://192.168.0.35/login.php?logout=true", true);
  xhttp.send();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      window.location.href = URL_PATH;
    }
  };
}
