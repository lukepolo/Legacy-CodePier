<template>
    <passport-personal-access-tokens></passport-personal-access-tokens>
</template>

<script>
    import {
        Clients,
        AuthorizedClients,
        PersonalAccessTokens,
    } from '../components/passport';

    export default {
        components: {
            Clients,
            AuthorizedClients,
            PersonalAccessTokens
        }
    }
</script>