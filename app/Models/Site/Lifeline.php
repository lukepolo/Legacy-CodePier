<?php

namespace App\Models\Site;

use App\Traits\Hashable;
use App\Models\SlackChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Lifeline extends Model
{
    use Hashable, Notifiable;

    protected $guarded = ['id'];

    protected $appends = [
        'url',
    ];

    protected $dates = [
        'last_seen',
        'created_at',
        'updated_at',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function getUrlAttribute()
    {
        return config('app.url_lifelines').'/'.$this->encode();
    }

    public function slackChannel()
    {
        return $this->morphOne(SlackChannel::class, 'slackable');
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->site->user->email;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        return $this->site->routeNotificationForSlack();
    }
}
