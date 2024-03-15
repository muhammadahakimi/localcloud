<html>
<head>
    <title>Login</title>
    <script src="jquery/jquery.js"></script>
    <script src="js/setup.js"></script>
    <script src="js/login.js"></script>
    <link rel="stylesheet" href="css/setup.css">
    <link rel="stylesheet" href="css/login.css">
    <style>
        
    </style>
</head>
<body>
    <div id="container">
        <div style="width:300px;padding:30px;">
            <img src="resources/images/cloud.webp" class="img_poster">
        </div>
        <div style="display:flex;align-items:center;width:300px;padding:30px;background:white;height:440px;border-radius: 0px 15px 15px 0px">
            <div id="div_login" style="display:block;width:100%;">
                <h2 style="text-align:center;">Welcome to Cloud</h2>
                <label class="labelinputbox">Username</label>
                <input type="text" id="input_userid" class="inputbox1" autocomplete="off" placeholder="Username">
                <label class="labelinputbox">Password</label>
                <input type="password" id="input_password" class="inputbox1" autocomplete="off" placeholder="********">
                <a href="#" style="display: block; margin-top:10px; font-size:14px;">Forgot Password?</a>
                <button id="btn_login" onclick="login()">Login</button>
                <hr>
                <button id="btn_register" onclick="open_register()">Register</button>
            </div>
            <div id="div_register" style="display:none;width:100%;">
                <label class="labelinputbox">Username</label>
                <input type="text" id="input_register_userid" class="inputbox1" autocomplete="off" placeholder="">
                <label class="labelinputbox">Name</label>
                <input type="text" id="input_register_name" class="inputbox1" autocomplete="off" placeholder="Ahmad Ali Bin Abu">
                <label class="labelinputbox">Phone</label>
                <input type="text" id="input_register_phone" class="inputbox1" autocomplete="off" placeholder="0191231234">
                <label class="labelinputbox">Email</label>
                <input type="text" id="input_register_email" class="inputbox1" autocomplete="off" placeholder="name@email.com">
                <label class="labelinputbox">Password</label>
                <input type="password" id="input_register_password" class="inputbox1" autocomplete="off" placeholder="********">
                <label class="labelinputbox">Confirm Password</label>
                <input type="password" id="input_register_password_confirm" class="inputbox1" autocomplete="off" placeholder="********">
                <button id="btn_login" onclick="register()">Register</button>
                <button id="btn_register" onclick="open_login()">Back to Login</button>
            </div>
        </div>
    </div>
    <div id="divLoading" class="overlay"><div><h2>Loading...</h2></div></div>
</body>
<script>
    document.getElementById("input_userid").addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            login();
        }
    });

    document.getElementById("input_password").addEventListener("keypress", function(event) {
        if (event.key === "Enter") {
            event.preventDefault();
            login();
        }
    });
</script>
</html>