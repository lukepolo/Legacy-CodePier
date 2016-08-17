<template>
    <section>
        <user-nav></user-nav>
        <passport-personal-access-tokens></passport-personal-access-tokens>
    </section>
</template>
<script>

    import UserNav from './components/UserNav.vue';
    import PassportClients from './components/passport/Clients.vue';
    import PassportAuthorizedClients from './components/passport/AuthorizedClients.vue';
    import PassportPersonalAccessTokens from './components/passport/PersonalAccessTokens.vue';
    export default {
        components : {
            UserNav,
            PassportClients,
            PassportAuthorizedClients,
            PassportPersonalAccessTokens
        }
    }
</script>