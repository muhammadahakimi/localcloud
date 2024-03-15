function loadingStart() {
    console.log("Loading Start");
    $("#divLoading").css("display","flex");
}

function loadingEnd() {
    console.log("Loading End");
    $("#divLoading").css("display","none");
}

function validatePhoneNumber(phoneNumber) {
    // Regular expression pattern for a valid phone number
    var phonePattern = /^\d{10}$/;
    
    // Test the input against the pattern
    return phonePattern.test(phoneNumber);
}

function validateEmail(email) {
    // Regular expression pattern for a valid email address
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    // Test the input against the pattern
    return emailPattern.test(email);
}