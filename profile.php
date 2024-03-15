<html>
<head>
    <title>Profile</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <script src="jquery/jquery.js"></script>
    <script src="js/setup.js"></script>
    <link rel="stylesheet" href="css/setup.css">
    <style>
        body {
            font-family: RubikRegular;
            margin: 0px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: lightgray;
        }

        body > div {
            background: white;
            padding: 20px;
            border-radius: 10px;
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 0px;
        }
    </style>
</head>
<body>
    <div>
        <h3>Profile</h3>
        <label class="labelinputbox">Username</label>
        <input id="input_username" class="inputbox">

        <label class="labelinputbox">Name</label>
        <input id="input_name" class="inputbox">
        <label class="labelinputbox">Phone</label>
        <input id="input_phone" class="inputbox">
        <label class="labelinputbox">Email</label>
        <input id="input_email" class="inputbox">
    </div>
</body>
<script>
    functtion update() {
        var data = {};
        data['name'] = $("input_name").val();
        data['phone'] = $("#input_phone").val();
        data['email'] = $("#input_email").val();
    }
</script>
</html>