<template>
    <ul class="nav nav-tabs">

        <router-link tag="li" class="wizard-item" :to="{ name : 'my_account' }" exact>
            <a>
                My Account
                <div class="small">Basic personal information</div>
            </a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'subscription' }" v-if="local()">
            <a>
                Subscription
                <div class="small">Sadly we require a subscription, but there are options for everyone!</div>
            </a>
        </router-link>
        <!--<router-link tag="li" class="wizard-item" to="/my-profile/oauth" v-if="local()">-->
            <!--<a>OAuth</a>-->
        <!--</router-link>-->
        <router-link tag="li" class="wizard-item" :to="{ name : 'user_ssh_keys' }">
            <a>
                SSH Keys
                <div class="small">Global SSH keys to access servers connected to this account</div>
            </a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user_server_providers' }">
            <a>
                Server Providers
                <div class="small">Connect to your existing server providers : Digital Ocean</div>
            </a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user_repository_providers' }">
            <a>
                Repository Providers
                <div class="small">We need basic repository access to allow deployments on your servers</div>
            </a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user_notification_providers' }">
            <a>
                Notification Settings
                <div class="small">Manage how we notify you about failed deployments, provisions and much more</div>
            </a>
        </router-link>

    </ul>
</template>
<script>
    export default {
        watch: {
            '$route': function() {
                $('#middle .section-content').scrollTop(0)
            }
        },
    }
</script>