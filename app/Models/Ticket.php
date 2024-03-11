<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'name',
        'email',
        'status',
        'message',
        'comment',
        'responsible_user_id',
    ];

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }
}
