<?php

namespace App\Models\User;

use Carbon\Carbon;
use App\Models\Pile;
use App\Models\SshKey;
use App\Traits\Hashable;
use App\Models\Site\Site;
use Laravel\Cashier\Card;
use App\Traits\HasServers;
use App\Models\Server\Server;
use Laravel\Cashier\Billable;
use Laravel\Passport\HasApiTokens;
use App\Models\Site\SiteDeployment;
use Illuminate\Notifications\Notifiable;
use Mpociot\Teamwork\Traits\UserHasTeams;
use App\Notifications\Channels\SlackMessageChannel;
use App\Notifications\Channels\DiscordMessageChannel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    const USER = 'user';
    const ADMIN = 'admin';

    protected $hashConnection = 'users';

    use Notifiable, UserHasTeams, Billable, HasApiTokens, HasServers, Hashable;

    protected $subscription;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'workflow',
        'confirmed',
        'current_pile_id',
        'second_auth_active',
        'second_auth_updated_at',
        'user_login_provider_id',
        'last_read_announcement',
        'referrer',
        'processing',
        'marketing',
        'last_bundle_download',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'second_auth_secret',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
        'last_login_at',
        'trial_ends_at',
        'last_bundle_download',
    ];

    protected $appends = [
        'is_subscribed',
        'subscription_plan',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsSubscribedAttribute()
    {
        return $this->attributes['is_subscribed'] = $this->subscribed();
    }

    public function getSubscriptionPlanAttribute()
    {
        $subscription = $this->subscription();
        return $this->attributes['subscription_plan'] = $this->subscribed() && ! empty($subscription) ? $subscription->stripe_plan : null;
    }

    public function getActivePlanAttribute()
    {
        $subscription = $this->subscriptionPlan;
        if (! empty($subscription) && $this->onTrial()) {
            $subscription = $this->subscription()->active_plan;
        }

        return $this->attributes['subscription_plan'] = $subscription;
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function provisionedServers()
    {
        return $this->hasMany(Server::class)->where('progress', '>=', '100');
    }

    public function userServerProviders()
    {
        return $this->hasMany(UserServerProvider::class);
    }

    public function userLoginProvider()
    {
        return $this->belongsTo(UserLoginProvider::class);
    }

    public function userRepositoryProviders()
    {
        return $this->hasMany(UserRepositoryProvider::class);
    }

    public function userNotificationProviders()
    {
        return $this->hasMany(UserNotificationProvider::class);
    }

    public function sshKeys()
    {
        return $this->morphToMany(SshKey::class, 'sshKeyable');
    }

    public function piles()
    {
        return $this->hasMany(Pile::class);
    }

    public function currentPile()
    {
        return $this->belongsTo(Pile::class, 'current_pile_id');
    }

    public function notificationSettings()
    {
        return $this->hasMany(UserNotificationSetting::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function hasRole($role)
    {
        return $this->role == $role;
    }

    public function getRunningCommands()
    {
        $commandsRunning = [];

        if ($this->currentPile) {
            $sites = $this->currentPile
                ->sites()
                ->with(['servers.commands' =>function ($query) {
                    $query->with('command')
                        ->where('failed', 0)
                        ->where('completed', 0);
                }])
                ->get();

            foreach ($sites as $site) {
                foreach ($site->servers as $server) {
                    foreach ($server->commands as $command) {
                        if ($command->command) {
                            $commandsRunning[$command->command->commandable_type][] = $command->command;
                        }
                    }
                }
            }
        }

        return collect($commandsRunning);
    }

    public function getRunningDeployments()
    {
        $deploymentsRunning = [];

        if ($this->currentPile) {
            $sites = $this->currentPile
                ->sites()
                ->whereHas('lastDeployment', function ($query) {
                    $query->whereIn('status', [
                        SiteDeployment::RUNNING,
                        SiteDeployment::QUEUED_FOR_DEPLOYMENT,
                    ]);
                })
                ->get();

            foreach ($sites as $site) {
                $deploymentsRunning[$site->id][] = $site->lastDeployment;
            }
        }

        return collect($deploymentsRunning);
    }

    public function getNotificationPreferences($notificationClass, $defaults = ['mail'], $required = [])
    {
        $this->load('notificationSettings.setting');

        $services = $defaults;

        $userNotification = $this->notificationSettings->keyBy('setting.event')->get($notificationClass);

        if (! empty($userNotification)) {
            $services = [];
            in_array('mail', $userNotification->services) ? $services[] = 'mail' : null;
            in_array('slack', $userNotification->services) ? $services[] = SlackMessageChannel::class : null;
            in_array('discord', $userNotification->services) ? $services[] = DiscordMessageChannel::class : null;
        }

        return array_merge($services, $required);
    }

    public function availableSslCertificates()
    {
        $this->load(['servers.sslCertificates' => function ($query) {
            $query->where('active', 1)
                ->orWhere('type', 'existing');
        }]);

        return $this->servers->map(function (Server $server) {
            return $server->sslCertificates;
        })->flatten()->unique('id')->keyBy('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Subscription Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Determine if the Stripe model has a given subscription.
     *
     * @param string      $subscription
     * @param string|null $plan
     *
     * @return bool
     */
    public function subscribed($subscription = 'default', $plan = null)
    {
        if ($this->role === 'admin') {
            return true;
        }

        if ($this->onTrial()) {
            return true;
        }

        $subscription = $this->subscription($subscription);

        if (is_null($subscription)) {
            return false;
        }

        if (is_null($plan)) {
            return $subscription->valid();
        }

        return $subscription->valid() && $subscription->active_plan === $plan;
    }

    public function getSubscriptionPrice()
    {
        if ($this->subscription()) {
            $price = $this->getStripeSubscription()->items->data[0]->plan->amount;

            $discount = $this->getSubscriptionDiscount();

            if (! empty($discount)) {
                if (is_int($discount)) {
                    $price -= $discount;
                } else {
                    if ($discount === '.100') {
                        $discount = 1;
                    }

                    $price = $price - ($price * $discount);
                }
            }

            return cents_to_dollars($price);
        }
    }

    public function getSubscriptionName()
    {
        if ($this->subscription()) {
            $subscriptionName = $this->getStripeSubscription()->plan->name;

            $discount = $this->getSubscriptionDiscount();

            if (! empty($discount)) {
                if (is_int($discount)) {
                    return $subscriptionName.' - $'.$discount.'.00 off';
                } else {
                    if ($discount === '.100') {
                        $discount = 1;
                    }

                    return $subscriptionName.' - %'.($discount * 100).' off';
                }
            }

            return $subscriptionName;
        }
    }

    public function getSubscriptionInterval()
    {
        if ($this->subscription()) {
            return $this->getStripeSubscription()->plan->interval;
        }
    }

    public function getSubscriptionDiscount()
    {
        if ($this->subscription()) {
            $discountObject = $this->getStripeSubscription()->discount;
            if (! empty($discountObject->start)) {
                $coupon = $discountObject->coupon;
                if ($coupon->amount_off) {
                    return $coupon->amount_off;
                } else {
                    return '.'.$coupon->percent_off;
                }
            }
        }
    }

    public function card()
    {
        if ($this->hasStripeId()) {
            $card = \Cache::rememberForever($this->id.'.card', function () {
                /** @var Card $card */
                $card = $this->cards()->first();
                if (! empty($card)) {
                    return $card->asStripeCard()->jsonSerialize();
                }
            });
        }

        if (! empty($card)) {
            return [
                'brand' => $card['brand'],
                'last4' => $card['last4'],
            ];
        }
    }

    public function getStripeSubscription()
    {
        if (empty($this->subscription)) {
            $this->subscription = \Cache::rememberForever($this->id.'.subscription', function () {
                $subscription = $this->subscription();
                if (! empty($subscription)) {
                    return $this->subscription()->asStripeSubscription();
                }
            });
        }

        return $this->subscription;
    }

    public function getNextBillingCycle()
    {
        if ($this->getStripeSubscription()) {
            return Carbon::createFromTimestamp(
                $this->getStripeSubscription()->current_period_end
            );
        }
    }

    public function canResume() {
        if ($this->subscription()) {
            return $this->subscription()->onGracePeriod() && $this->subscription()->cancelled();
        }
        return false;
    }

    public function canUpdate() {
        if ($this->subscription()) {
            return !$this->subscription()->cancelled() || $this->onGenericTrial() || $this->onTrial();
        }
        return false;
    }

    public function isCanceled() {
        if ($this->subscription()) {
            return !$this->subscription()->onGracePeriod() && $this->subscription()->cancelled();
        }
        return false;
    }

    public function subscriptionInfo()
    {
        $this->refresh();

        $subscription =  $this->subscription();

        return [
            'card'                 => $this->card(),
            'canResume'            => $this->canResume(),
            'canUpdate'            => $this->canUpdate(),
            'isCanceled'           => $this->isCanceled(),
            'subscribed'           => $this->subscribed(),
            'subscription'         => $this->subscription(),
            'isOnTrail'            => $this->onGenericTrial(),
            'subscriptionEnds'     => $this->getNextBillingCycle(),
            'subscriptionName'     => $this->getSubscriptionName(),
            'subscriptionPrice'    => $this->getSubscriptionPrice(),
            'subscriptionInterval' => $this->getSubscriptionInterval(),
            'subscriptionEnded'    => $subscription ? $subscription->ended() : false,
            'subscriptionDiscount' => $this->getStripeSubscription() ? $this->getStripeSubscription()->discount : null,
        ];
    }
}
