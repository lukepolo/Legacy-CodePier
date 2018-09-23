<template>
    <ul class="nav nav-tabs">
        <router-link tag="li" class="wizard-item" :to="{ name : 'user.account' }" exact>
            <a>
                My Account
                <div class="small">Basic personal information</div>
            </a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user.subscription' }">
            <a>
                Subscription
                <div class="small">Get more features by switching plans, there are options for everyone!</div>
            </a>
        </router-link>
        <router-link tag="li" class="wizard-item" :to="{ name : 'user.ssh-keys' }">
            <a>
                SSH Keys
                <div class="small">Global SSH keys to access servers connected to this account</div>
            </a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user.server-providers' }">
            <a>
                Server Providers
                <div class="small">Connect to your existing server providers : Digital Ocean / Linode</div>
            </a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user.source-control-providers' }">
            <a>
                Source Control Providers
                <div class="small">You can make your life easier by attaching a github / bitbucket / gitlab account to allow us to deploy your code</div>
            </a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user.notification-settings' }">
            <a>
                Notification Settings
                <div class="small">Manage how we notify you about failed deployments, provisions and much more</div>
            </a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user.privacy' }">
            <a>
                Privacy Settings
                <div class="small">Manage how handle your data</div>
            </a>
        </router-link>

    </ul>
</template>
