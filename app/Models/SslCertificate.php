<?php

namespace App\Models;

use App\Models\Site\Site;
use App\Traits\Encryptable;
use App\Traits\HasServers;
use App\Models\Server\Server;
use Illuminate\Database\Eloquent\Model;

class SslCertificate extends Model
{
    use HasServers, Encryptable;

    protected $guarded = ['id'];

    protected $encryptable = [
        'acme_password'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['unencrypted_acme_password'];

    /**
     * Get the administrator flag for the user.
     *
     * @return bool
     */
    public function getUnencryptedAcmePasswordAttribute()
    {
        if (! empty($this->attributes['acme_password'])) {
            return decrypt($this->attributes['acme_password']);
        }
    }

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
