<?php

namespace App\Models;

use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Model;

class SshKey extends Model
{
    use Encryptable;

    protected $guarded = ['id'];

    protected $encryptable = [
        'ssh_key',
    ];

    protected $hidden = [
        'ssh_key',
    ];
}
