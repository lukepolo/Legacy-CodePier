<template>
    <div v-if="site">
        SSH Keys
    </div>
</template>

<script>
    export default {
        components : {
        },
        data() {
            return {

            }
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getSite', this.$route.params.site_id);
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            }
        }
    }
</script>