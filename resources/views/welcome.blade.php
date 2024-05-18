<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <title>status</title>
        <style>
            * {
                margin: 0;
                padding: 0;
            }
        </style>
        @vite(['resources/js/app.js'])
        
        {{-- <script src="{{asset('app-BZnBpbUj.js')}}"></script> --}}
        <script>
            
            // console.log(port)
            // $.post('https://0.0.0.0:8080', data)
            // .done(function(response){
            //     console.log("I finally got a response from the server!");
            // });
    
            // console.log("I'm not gonna wait for that response.");
            // console.log('page loaded')
            window.addEventListener('DOMContentLoaded',function(){
                console.log('your live')
               
                    // window.Echo.channel('chats')
                    // .listen('TestEvent', (e) => {
                    //         console.log('test successful ',e.msg)
                    // })
                    Echo.join(`onlineChannel`)
                        .here((users) => {
                            // ...
                        })
                        .joining((user) => {
                            console.log(user.name);
                        })
                        .leaving((user) => {
                            console.log(user.name);
                        })
                        .error((error) => {
                            console.error(error);
                        });
                    window.Echo.channel('chatChannel_3')
                    .listen('SendMessageEvent', (e) => {
                            console.log('chat ',e.message)
                    })
                    window.Echo.channel('channelUpdates_1')
                    .listen('ChannelUpdatesEvent', (e) => {
                            console.log('post ',e.post)
                    })
                    window.Echo.channel('commentsChannel_2')
                    .listen('CommentEvent', (e) => {
                            console.log('comment',e.comment)
                    })
                
            })
        </script>
    </head>
    <body>
        
        <div
            style="
                height: 100vh;
                width: 100%;
                background: black;
                display: flex;
                justify-content: center;
                align-items: center;
            "
        >
            <div style="">
                <img src="{{ URL('logo.png') }}" alt="" />
            </div>
        </div>
    </body>
</html>
