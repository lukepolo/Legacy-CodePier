<?php

namespace App\Models\Server;

use App\Models\User\User;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class ProvisioningKey extends Model
{
    use ConnectedToUser;

    protected $primaryKey = 'key';
    protected $guarded = ['id'];

    public $incrementing = false;

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public static function findByKey($key)
    {
        return self::where('key', $key)->first();
    }

    public static function generate(User $user, Server $server)
    {
        $key = self::create([
            'user_id' => $user->id,
            'key' => str_random(40),
            'server_id' => $server->id,
        ]);

        return $key;
    }

    public static function isUsed($key) {
        if (self::find($key)->used == true) {
            return true;
        }

        return false;
    }
}
