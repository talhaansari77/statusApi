<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{$data['title']}}</title>
</head>
<body>
    

    <h2>Status Password Reset Code</h2>
    
    {{-- <p>{{$data['body']}}</p> --}}
    <h3>Use this code to replace your password.</h3>
    
    <h3>{{$data['token']}}</h3>

    {{-- <a href="{{$data['url']}}">Click Here To Reset Your Password</a> --}}
    
</body>
</html>