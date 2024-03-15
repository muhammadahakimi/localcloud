function open_register() {
    $("#div_register").show();
    $("#div_login").hide();
}

function open_login() {
    $("#div_register").hide();
    $("#div_login").show();
}

function login() {
    if (validation()) {
        loadingStart();
        var data = {};
        data['userid'] = $("#input_userid").val();
        data['password'] = $("#input_password").val();
        $.ajax({
            url: 'server/login.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                if (response.result) {
                    location.replace("dashboard.php");
                } else {
                    alert(response.reason);
                }
                loadingEnd();
            }
        });
    }
}

function validation() {
    if ($("#input_userid").val() == "") {
        console.log("input userid is empty");
        $("#input_userid").focus()
        return false;
    }
    if ($("#input_password").val() == "") {
        console.log("input password is empty");
        $("#input_password").focus()
        return false;
    }

    return true;
}

function validation_register() {
    if ($("#input_register_userid").val() == "") {
        console.log("input userid is empty");
        $("#input_register_userid").focus();
        return false;
    }
    if ($("#input_register_name").val() == "") {
        console.log("input name is empty");
        $("#input_register_name").focus();
        return false;
    }
    if ($("#input_register_phone").val() == "") {
        console.log("input phone is empty");
        $("#input_register_phone").focus();
        return false;
    }
    if (!validatePhoneNumber($("#input_register_phone").val())) {
        console.log("invalid phone number");
        $("#input_register_phone").focus();
        return false;
    }
    if ($("#input_register_email").val() == "") {
        console.log("input email is empty");
        $("#input_register_email").focus();
        return false;
    }
    if (!validateEmail($("#input_register_email").val())) {
        console.log("invalid email address");
        $("#input_register_email").focus();
        return false;
    }
    if ($("#input_register_password").val() == "") {
        console.log("input password is empty");
        $("#input_register_password").focus();
        return false;
    }
    if ($("#input_register_password_confirm").val() == "") {
        console.log("input confirm password is empty");
        $("#input_register_password_confirm").focus();
        return false;
    }
    if ($("#input_register_password").val() != $("#input_register_password_confirm").val()) {
        console.log("password not macthing");
        $("#input_register_password_confirm").focus();
        return false;
    }

    return true;
}

function register() {
    if (validation_register()) {
        loadingStart();
        var data = {};
        data['userid'] = $("#input_register_userid").val();
        data['name'] = $("#input_register_name").val();
        data['phone'] = $("#input_register_phone").val();
        data['email'] = $("#input_register_email").val();
        data['password'] = $("#input_register_password").val();
        $.ajax({
            url: 'server/register.php',
            type: 'post',
            data: data,
            dataType: 'JSON',
            success: function (response) {
                console.log(response);
                if (response.result) {
                    location.replace("login.php");
                }
                loadingEnd();
            }
        });
    }
}