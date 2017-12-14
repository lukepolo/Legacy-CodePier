@component('mail::message')
# Welcome to the beta!

@component('vendor.mail.html.cover')

@component('mail::button', ['url' => 'https://codepier.io/login'])
Login Now
@endcomponent

@endcomponent


It's important that you remember we are still developing the application and some features have been turned off. This is so we can test the core features.
Will will notify you when new features are ready for you to test.

As you can imagine, the system is quite complicated. There will be a lot happening when you click around.
This is where you can help. There will be bugs. Help us by <a href="https://github.com/CodePier/CodePier-Issues/issues" target="_blank">adding an issue on Github.</a>

---

We appreciate all of the help you can provide us during the Beta period to make CodePier the best it can be.
When you sign up for the Beta, you will receive a discounted price for CodePier once officially launched, that will be valid for one year.
Users will be eligible for discounts between 20-50% dependent on your activity and participation during the Beta.

### With that in mind, during our Beta 1 we will be testing the following:

## Account Setup
* SSH Keys
* Server Providers
* Repository Providers
* Notification Providers
* Slack Integration

## Basic Flow Of App
* Piles (organizational groups for your sites)
* Site Creation
* Site Requirements (tabs when under the site)
* Server Creation

## Site Setup
* Customization of Deployments
* SSL Certificates (both lets encrypt and standard certificates)
* Site Files
* And more

## Server Provisioning
* Full Stack Server Provisioning (based on your sites requirements)
* Custom Servers

## Site Deployments
* Setup of Site Deployments
* Automatic Deployments
* Site Deployment Webhooks

## Server Configuration
* Customizing Servers (through the server tabs)
* Server Monitoring when Viewing Sites
* Multiple Servers for One Site
* Server Files
* And more

I know this is a lot, but I hope I can get you as excited as I am. With the way the core app is built, many of these features should be available quickly.
Again, I'll provide updates when there are major feature releases.

In addition to <a href="https://github.com/CodePier/CodePier-Issues/issues" target="_blank">adding issues to Github</a>,
we also have setup a Slack channel that you can join. You will find the link in the application in the lower left hand corner when you log in. Feel free chat with other Beta users, or contact us directly
to shoot us any questions, suggestions, etc.


@slot('subcopy')
## A Look Ahead

### Here is a list of major upcoming features that will be released during the Betas:

* Buoys (self contained 1 click apps)
* Bitts (bash scripts to run on server)
* Additional Notification Settings
* Additional PHP Frameworks (based on what you guys want to see next)
* Additional Languages (Ruby first)
* Search Functionality (for quick switching between sites / servers)
* Database Backup System
* Cloning of Sites to make it easier to deploy on a different pile (great use for setting up multiple environments)
* Savable Server Features
* Teams
* API
* Different Types of Servers
* Vertical / Horizontal Scaling
* DNS Quick View
* Security Checks Based on Language
@endslot

## Thanks,
### Luke @ {{ config('app.name') }}
@endcomponent
