<template>
    <section>
        <server-features dispatch="updateSiteServerFeatures"></server-features>
    </section>
</template>

<script>
    import ServerFeatures from './../../components/ServerFeatures.vue'
    export default {
        components: {
            ServerFeatures
        }
    }
</script>