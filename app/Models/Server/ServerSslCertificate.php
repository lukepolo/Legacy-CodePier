<?php

namespace App\Models\Server;

use App\Models\Site\SiteSslCertificate;
use App\Traits\ConnectedToUser;
use Illuminate\Database\Eloquent\Model;

class ServerSslCertificate extends Model
{
    use ConnectedToUser;

    protected $guarded = ['id'];

    static $userModel = 'server';

    public function siteSslCertificate()
    {
        return $this->belongsTo(SiteSslCertificate::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
