<?php

namespace App\Models;

use App\Models\Server\Server;
use App\Models\Site\Site;
use App\Traits\HasServers;
use Illuminate\Database\Eloquent\Model;

class SslCertificate extends Model
{
    use HasServers;

    protected $guarded = ['id'];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->morphedByMany(Site::class, 'sslCertificateable');
    }

    public function servers()
    {
        return $this->morphedByMany(Server::class, 'sslCertificateable');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */
    public function delete()
    {
        $this->sites()->detach();
        $this->servers()->detach();
        parent::delete();
    }

    public function commandDescription($status)
    {
        return $status.' SSL certificate '.$this->domains;
    }
}
