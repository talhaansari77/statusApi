<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('testChannel', function () {
    return true;
});
Broadcast::channel('onlineChannel', function ($user) {
    return $user;
});
