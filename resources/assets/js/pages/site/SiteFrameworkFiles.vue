<template>
    <section>
        <div class="section-content">
            <div class="container">
                <template v-if="files && site">
                    <site-file :site="site" :servers="site.servers" :file="file" v-for="file in files"></site-file>
                </template>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getSite', this.$route.params.site_id);
                this.$store.dispatch('getEditableFrameworkFiles', this.$route.params.site_id);
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            files() {
                return this.$store.state.serversStoreeditable_framework_files;
            }
        },
    }
</script>