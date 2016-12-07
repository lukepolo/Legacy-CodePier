<template>
    <ul class="wizard">
        <router-link tag="li" class="wizard-item" to="/my-profile" exact>
            <a>My Profile</a>
        </router-link>
        <router-link tag="li" class="wizard-item" to="/my-profile/oauth">
            <a>OAuth</a>
        </router-link>
        <router-link tag="li" class="wizard-item" to="/my-profile/ssh-keys">
            <a>SSH Keys</a>
        </router-link>
        <router-link tag="li" class="wizard-item" to="/my-profile/subscription">
            <a>Subscription</a>
        </router-link>
        <router-link tag="li" class="wizard-item" to="/my-profile/server-providers">
            <a>Server Providers</a>
        </router-link>
        <router-link tag="li" class="wizard-item" to="/my-profile/repository-providers">
            <a>Repository Providers</a>
        </router-link>
        <router-link tag="li" class="wizard-item" to="/my-profile/notification-providers">
            <a>Notification Providers</a>
        </router-link>
    </ul>
</template>