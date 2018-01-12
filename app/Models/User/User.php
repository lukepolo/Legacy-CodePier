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
        'referrer',
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
        'trial_ends_at',
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
        return $this->attributes['subscription_plan'] = $this->subscribed() && ! empty($this->subscription()) ? $this->subscription()->stripe_plan : null;
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

    public function userLoginProviders()
    {
        return $this->hasMany(UserLoginProvider::class);
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
                    $query->where('failed', 0)
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

    public function getNotificationPreferences($notificationClass, $defaults = [], $required = [])
    {
        $this->load('notificationSettings.setting');

        $services = $defaults;

        $userNotification = $this->notificationSettings->keyBy('setting.event')->get($notificationClass);

        if (! empty($userNotification)) {
            $services = array_replace($userNotification->services,
                array_fill_keys(
                    array_keys($userNotification->services, 'slack'),
                    SlackMessageChannel::class
                )
            );
        }

        return array_merge($services, $required);
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
        if ($this->subscribed() && $this->hasStripeId()) {
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

    public function subscriptionInfo()
    {
        $this->refresh();

        return [
            'card'                 => $this->card(),
            'switchingPlans'       => $this->onTrial(),
            'subscribed'           => $this->subscribed(),
            'subscription'         => $this->subscription(),
            'isOnTrail'            => $this->onGenericTrial(),
            'subscriptionEnds'     => $this->getNextBillingCycle(),
            'subscriptionName'     => $this->getSubscriptionName(),
            'subscriptionPrice'    => $this->getSubscriptionPrice(),
            'subscriptionInterval' => $this->getSubscriptionInterval(),
            'subscriptionDiscount' => $this->getStripeSubscription() ? $this->getStripeSubscription()->discount : null,
        ];
    }
}
