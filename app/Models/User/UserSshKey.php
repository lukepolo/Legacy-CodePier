<?php

namespace App\Models\User;

use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class UserSshKey extends Model
{
    use ConnectedToUser;

    protected $guarded = ['id'];
}
