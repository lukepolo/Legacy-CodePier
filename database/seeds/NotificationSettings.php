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
            \App\Notifications\Server\ServerDiskUsage::class => [
                'name' => 'High Server Disk Usage',
                'default' => true,
                'description' => "Sends a notification when a server's disk usage is high.",
                'services' => [
                    self::MAIL,
                    self::SLACK,
                ],
            ],
            \App\Notifications\Server\ServerLoad::class => [
                'name' => 'High Server CPU Usage',
                'default' => true,
                'description' => "Sends a notification when a server's CPU usage is high.",
                'services' => [
                    self::MAIL,
                    self::SLACK,
                ],
            ],
            \App\Notifications\Server\ServerMemory::class => [
                'name' => 'High Server Memory Usage',
                'default' => true,
                'description' => "Sends a notification when a server's memory usage is high.",
                'services' => [
                    self::MAIL,
                    self::SLACK,
                ],
            ],
            \App\Notifications\Server\ServerProvisioned::class => [
                'name' => 'Server Provisioned',
                'default' => true,
                'description' => 'Sends a notification when a server has been provisioned.',
                'services' => [
                    self::MAIL,
                ],
            ],
            \App\Notifications\BuoyInstall::class => [
                'name' => 'Buoy Installed',
                'default' => true,
                'description' => 'Sends a notification when a Buoy has been installed.',
                'services' => [
                    self::MAIL,
                ],
            ],
            \App\Notifications\LifeLineCheckedIn::class => [
                'name' => 'Lifeline Check-in',
                'default' => true,
                'description' => 'Sends a notification when a Lifeline has checked in.',
                'services' => [
                    self::MAIL,
                    self::SLACK,
                ],
            ],
            \App\Notifications\LifeLineThresholdExceeded::class => [
                'name' => "Lifeline Hasn't Checked In",
                'default' => true,
                'description' => 'Sends a notification when a Lifeline has not preformed a check-in for some time.',
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
