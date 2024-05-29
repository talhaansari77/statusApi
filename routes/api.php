<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\InNotificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StatusChannelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserSettings;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BlockListController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FollowingController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post("/signup", [UserController::class, "register"]);
Route::post("/resendOtp", [UserController::class, "resendOtp"]);
Route::post("/login", [UserController::class, "authenticate"]);
Route::post("/verifyOtp", [UserController::class, "verifyOtp"]);
Route::post("/forgetPassword", [UserController::class, "forgetPassword"]);
Route::post("/verifyResetToken", [UserController::class, "verifyResetToken"]);
Route::post("/resetPassword", [UserController::class, "resetPassword"]);
    // Route::post("/deleteUser", [UserSettings::class, "deleteUser"]);

Route::middleware(['auth:sanctum', 'verified'])->group(static function () {
    //getAuth
    Route::post("/getAuth", [UserController::class, "getAuth"]);
    Route::post("/getUserDetail", [UserController::class, "getUserDetail"]);
    
    // user Settings
    Route::post("/updateSettings", [UserSettings::class, "updateSettings"]);
    Route::post("/changeUserPassword", [UserSettings::class, "changeUserPassword"]);
    Route::post("/changeUserEmail", [UserSettings::class, "changeUserEmail"]);
    Route::post("/deleteUser", [UserSettings::class, "deleteUser"]);
    Route::post("/logout", [UserSettings::class, "logout"]);
    // setup
    Route::post("/profileSetup", [UserController::class, "profileSetup"]);
    
    //Following & Followers
    Route::post("/follow", [FollowingController::class, "follow"]);
    Route::post("/isFollowing", [FollowingController::class, "isFollowing"]);
    Route::post("/getUsers", [FollowingController::class, "getUsers"]);
    //search
    Route::post("/searchUserByName", [SearchController::class, "searchUserByName"]);
    Route::post("/searchUserWithFilter", [SearchController::class, "searchUserWithFilter"]);
    //blockList
    Route::post("/blockUser", [BlockListController::class, "blockUser"]);
    Route::post("/getBlockedUsers", [BlockListController::class, "getBlockedUsers"]);
    //createComment
    Route::post("/createComment", [CommentsController::class, "createComment"]);
    Route::post("/getUserComment", [CommentsController::class, "getUserComment"]);
    Route::post("/deleteComment", [CommentsController::class, "deleteComment"]);
    //createFavorite
    Route::post("/favorite", [FavoriteController::class, "favorite"]);
    Route::post("/isFavorite", [FavoriteController::class, "isFavorite"]);
    Route::post("/getFavoriteUsers", [FavoriteController::class, "getFavoriteUsers"]);
    
    //send chat 
    Route::post("/sendChat", [CommentsController::class, "sendChat"]);
    //create post
    Route::post("/getStatus/{user}", [PostController::class, "getStatus"]);
    Route::post("/createPost", [PostController::class, "createPost"]);
    Route::post("/addRemoveLikes", [PostController::class, "addRemoveLikes"]);
    Route::post("/addRemoveViews", [PostController::class, "addRemoveViews"]);
    Route::post("/updatePost", [PostController::class, "updatePost"]);
    Route::post("/deletePost/{post}", [PostController::class, "deletePost"]);
    //channel controller
    Route::post("/getChannel", [StatusChannelController::class, "getChannel"]);
    Route::post("/getFollowingChannel", [StatusChannelController::class, "getFollowingChannel"]);
    Route::post("/getFavoritesChannel", [StatusChannelController::class, "getFavoritesChannel"]);
    //chat
    Route::post("/sendMessage", [MessageController::class, "sendMessage"]);
    Route::post("/readMessage/{message}", [MessageController::class, "readMessage"]);
    Route::post("/getChatList", [MessageController::class, "getChatList"]);
    Route::post("/getConversation", [MessageController::class, "getConversation"]);
    Route::post("/searchMessages/{userId}/{search}", [MessageController::class, "searchMessages"]);
    Route::post("/deleteConversation/{conversation}", [MessageController::class, "deleteConversation"]);
    Route::post("/getConversationIfExist", [MessageController::class, "getConversationIfExist"]);
    Route::post("/startTypingChannel", [MessageController::class, "startTypingChannel"]);
    
    
    //archive
    Route::post("/createArchive", [ArchiveController::class, "createArchive"]);
    Route::post("/getArchive/{archive}", [ArchiveController::class, "getArchive"]);
    Route::post("/getUserArchives/{userId}", [ArchiveController::class, "getUserArchives"]);
    //favoriteConversation
    Route::post("/createFavoriteConversation", [ArchiveController::class, "createFavoriteConversation"]);
    Route::post("/getFavoriteConversation/{favoriteConversation}", [ArchiveController::class, "getFavoriteConversation"]);
    Route::post("/getUserFavoriteConversation/{userId}", [ArchiveController::class, "getUserFavoriteConversation"]);
    //Trash
    Route::post("/createTrashConversation", [ArchiveController::class, "createTrashConversation"]);
    Route::post("/getTrashConversation/{trashConversation}", [ArchiveController::class, "getTrashConversation"]);
    Route::post("/getUserTrashConversation/{userId}", [ArchiveController::class, "getUserTrashConversation"]);
    //BlockConversation
    Route::post("/createBlockConversation", [ArchiveController::class, "createBlockConversation"]);
    Route::post("/getBlockConversation/{blockChat}", [ArchiveController::class, "getBlockConversation"]);
    Route::post("/getUserBlockConversation/{userId}", [ArchiveController::class, "getUserBlockConversation"]);
    //InNotification
    Route::post("/getInNotifications", [InNotificationController::class, "getInNotifications"]);
    // Route::get("users", [UserController::class, "index"]);
    // Route::post('logout', [UserController::class, 'logout']);
    // // product management
    // Route::apiResource('products', ProductsController::class);
    
});