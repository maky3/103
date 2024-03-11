<?php
namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicyns
{
use HandlesAuthorization;

// ...

/**
* Determine whether the user is the responsible user for the ticket.
*
* @param  \App\Models\User  $user
* @param  \App\Models\Ticket  $ticket
* @return mixed
*/
public function isResponsibleUser(User $user, Ticket $ticket)
{
return $user->id === $ticket->responsible_user_id;
}
}
