<?php

use Illuminate\Database\Seeder;

class NotificationSettings extends Seeder
{
    const SLACK = \App\Http\Controllers\Auth\OauthController::SLACK;
    const MAIL = 'mail';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings = [
            \App\Notifications\Site\NewSiteDeployment::class => [
                'name' => 'New Site Deployment',
                'default' => true,
                'description' => 'Sends a notification that a deployment has been queued',
                'services' => [
                    self::MAIL,
                    self::SLACK,
                ],
            ],
            \App\Notifications\Site\SiteDeploymentFailed::class => [
                'name' => 'Site Deployment Failed',
                'default' => true,
                'description' => 'Sends a notification that a deployment failed with details of the error and server',
                'services' => [
                    self::MAIL,
                    self::SLACK,
                ],
            ],
            \App\Notifications\Site\SiteDeploymentSuccessful::class => [
                'name' => 'Site Deployment Successful',
                'default' => true,
                'description' => 'Sends a notification that a deployment has been been successful for all servers',
                'services' => [
                    self::MAIL,
                    self::SLACK,
                ],
            ],
        ];

        foreach ($settings as $event => $data) {

            $notificationSetting = \App\Models\NotificationSetting::firstOrNew([
                'event' => $event,
            ]);

            $notificationSetting->fill([
                'name' => $data['name'],
                'default' => $data['default'],
                'services' => $data['services'],
                'description' => $data['description'],
            ]);

            $notificationSetting->save();
        }
    }
}
