<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <user-nav></user-nav>
            <passport-personal-access-tokens></passport-personal-access-tokens>
        </section>
    </section>
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