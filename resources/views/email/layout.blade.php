<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LION PORTAL</title>
    @stack('styles')
</head>

<body>
    <div style="border: 1px solid #e3e3e3; border-radius: 10px; background-color: #ffffff;width: 100%;max-width: 600px;margin: 0 auto;font-family: sans-serif; font-size:14px;">
        <div style="border-top-left-radius: 10px; border-top-right-radius: 10px;height:130px;background-position: top; background-size:cover; background-image: url('https://i.ibb.co/bm7RsBm/Header.jpg');">
        </div>
        <div style="height:85px;color:#30a543; background-position: bottom; background-size:cover; background-image: url('https://i.ibb.co/6Y5mv6m/Footer.png');">
            <p style="text-align:center; font-weight: bold; margin-top:0px;">@yield('title')</p>
        </div>
        <div style="padding:30px;word-wrap: break-word;line-height: 25px;">
            @yield('content')
        </div>

        <div style="border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;background-image: url('https://i.ibb.co/6Y5mv6m/Footer.png'); background-size: cover; background-position: bottom; min-height:10px;padding:30px; color:#30a543">
            Best regards,<br />
            LION - IT TEAM.
        </div>

    </div>
</body>

</html>