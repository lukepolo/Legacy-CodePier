<template>
    <section>
        <passport-personal-access-tokens></passport-personal-access-tokens>
    </section>
</template>
<script>

    import PassportClients from './components/passport/Clients.vue';
    import PassportAuthorizedClients from './components/passport/AuthorizedClients.vue';
    import PassportPersonalAccessTokens from './components/passport/PersonalAccessTokens.vue';
    export default {
        components : {
            PassportClients,
            PassportAuthorizedClients,
            PassportPersonalAccessTokens
        }
    }
</script>