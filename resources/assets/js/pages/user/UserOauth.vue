<template>
    <passport-personal-access-tokens></passport-personal-access-tokens>
</template>
<script>

    import UserNav from './components/UserNav.vue';
    import PassportClients from './components/passport/Clients.vue';
    import PassportAuthorizedClients from './components/passport/AuthorizedClients.vue';
    import PassportPersonalAccessTokens from './components/passport/PersonalAccessTokens.vue';
    import LeftNav from './../../core/LeftNav.vue';
    export default {
        components: {
            LeftNav,
            UserNav,
            PassportClients,
            PassportAuthorizedClients,
            PassportPersonalAccessTokens
        }
    }
</script>