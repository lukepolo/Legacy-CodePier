<?php

namespace App\Console\Commands;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionPlans;
use Stripe\Plan;
use Stripe\Stripe;
use Illuminate\Console\Command;

class UpdateSubscriptionPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the subscription plans.';

    /**
     * ServerOptionController constructor.
     */
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach(Plan::all()->data as $plan) {
            $planModel = SubscriptionPlan::firstOrNew([
                'plan_id' => $plan->id
            ]);

            $planModel->fill([
                'name' => $plan->name,
                'amount' => $plan->amount,
                'currency' => $plan->currency,
                'livemode' => $plan->livemode,
                'interval' => $plan->interval,
                'metadata' => $plan->metadata,
                'interval_count' => $plan->interval_count,
                'trial_period_days' => $plan->trial_period_days,
                'statement_descriptor' => $plan->statement_descriptor,
            ]);

            $planModel->save();
        }
    }
}


