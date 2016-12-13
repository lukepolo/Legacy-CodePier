<?php

namespace App\Models\User;

use App\Traits\Encryptable;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class UserSshKey extends Model
{
    use ConnectedToUser, Encryptable;

    protected $guarded = ['id'];

    protected $encryptable = [
        'ssh_key',
    ];

    protected $hidden = [
        'ssh_key',
    ];
}
