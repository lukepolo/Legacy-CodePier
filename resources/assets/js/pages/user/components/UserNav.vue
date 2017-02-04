<template>
    <ul class="nav nav-tabs">

        <router-link tag="li" class="wizard-item" :to="{ name : 'my_account' }" exact>
            <a>My Account</a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'subscription' }">
            <a>Subscription</a>
        </router-link>
        <!--<router-link tag="li" class="wizard-item" to="/my-profile/oauth">-->
        <!--<a>OAuth</a>-->
        <!--</router-link>-->
        <router-link tag="li" class="wizard-item" :to="{ name : 'user_ssh_keys' }">
            <a>SSH Keys</a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user_server_providers' }">
            <a>Server Providers</a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user_repository_providers' }">
            <a>Repository Providers</a>
        </router-link>

        <router-link tag="li" class="wizard-item" :to="{ name : 'user_notification_providers' }">
            <a>Notification Settings</a>
        </router-link>
    </ul>
</template>
<script>
    export default{

    }
</script>