$(document).ready(function() {
  // Convert the UTC date and time to the user's local time
  function convertToLocalTime(utcDate) {
    // Get the user's timezone offset in minutes
    var timezoneOffset = new Date().getTimezoneOffset();

    // Convert the UTC date and time to milliseconds
    var utcTimestamp = Date.parse(utcDate);

    // Calculate the local date and time by adding the timezone offset to the UTC timestamp
    var localTimestamp = utcTimestamp + timezoneOffset * 60 * 1000;

    // Convert the local timestamp to a Date object
    var localDate = new Date(localTimestamp);

    // Return the local date and time as a string
    return localDate.toLocaleString();
  }

  // Show the API key
  $("#show-api-key").click(function() {
    $(".api-key-wrapper").show();
    $("#show-api-key").hide();
    $("#hide-api-key").show();
  });

  // Hide the API key
  $("#hide-api-key").click(function() {
    $(".api-key-wrapper").hide();
    $("#show-api-key").show();
    $("#hide-api-key").hide();
  });

  // Convert the signup date and last login time to the user's local time
  var signupDate = "<?php echo $_SESSION['signup_date']; ?>";
  var lastLogin = "<?php echo $_SESSION['last_login']; ?>";
  $("#signup-date").text(convertToLocalTime(signupDate));
  $("#last-login").text(convertToLocalTime(lastLogin));
});
