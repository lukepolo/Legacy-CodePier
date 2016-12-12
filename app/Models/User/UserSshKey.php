<?php

namespace App\Models\User;

use App\Traits\ConnectedToUser;
use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class UserSshKey extends Model
{
    use ConnectedToUser, Encryptable;

    protected $guarded = ['id'];

    protected $encryptable = [
        'ssh_key',
    ];
}
