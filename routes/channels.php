<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


// Additional Steps for Private Channels
// If you stick with PrivateChannel, you need to:

// Set Up Broadcasting Authentication:
// In routes/channels.php, define the authorization logic

// CODE ------- >
Broadcast::channel('admin-notifications', function ($user) {
    // Only allow admins to listen
    return $user->isAdmin(); // Replace with your admin check logic
});

Broadcast::channel('technician-notifications', function ($user) {
    // Only allow admins to listen
    return $user->isTechnician(); // Replace with your admin check logic
});