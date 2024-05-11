<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OTP</title>
</head>
<body>
   
    <h2>Your Code for Status</h2>
    

    {{-- {{ $Username }} --}}
    <h3>Thanks for joining status!</h3>
    <h3>Use the Code below in the app to verify your email.</h3>
    <h3>{{$data['otp']}}</h3>

    <p>Please consider joining our advisory community:</p>
    <a href="https://www.gofundme.com/f/statusapp">https://www.gofundme.com/f/statusapp</a>
</body>
</html>