<?php

namespace App\Models\Server;

use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class ServerStat extends Model
{
    use ConnectedToUser;

    protected $guarded = ['id'];

    protected $dates = [
        'disk_stat_last_updated_at',
        'load_stat_last_updated_at',
        'memory_stat_last_updated_at'
    ];

    protected $casts = [
        'disk_stats' => 'json',
        'load_stats' => 'json',
        'memory_stats' => 'json',
        'disk_notification_count' => 'json',
        'memory_notification_count' => 'json',
    ];

    public static $userModel = 'server';

    public $timestamps = false;

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
