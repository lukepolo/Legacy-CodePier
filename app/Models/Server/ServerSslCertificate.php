<?php

namespace App\Models\Server;

use App\Models\Site\SiteSslCertificate;
use App\Traits\ServerCommands;
use Illuminate\Database\Eloquent\Model;

class ServerSslCertificate extends Model
{
    protected $guarded = ['id'];

    public function siteSslCertificate()
    {
        return $this->belongsTo(SiteSslCertificate::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
