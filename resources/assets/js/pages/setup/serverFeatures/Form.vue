<template>
    <section>
        <div class="jcf-form-wrap">

            <template v-if="!server && update != false">

                <form @submit.prevent="dispatchMethod" enctype="multipart/form-data" class="floating-labels">

                    <features></features>

                    <div class="btn-footer">
                        <button class="btn btn-primary" type="submit">Update Site Server Features</button>
                    </div>

                </form>

            </template>
            <template v-else>

                <features></features>

            </template>

        </div>

    </section>
</template>

<script>
    import Features from './components/Features.vue'

    export default {
        props : ["update"],
        components: {
            Features
        },
        methods : {
            dispatchMethod() {
                this.$store.dispatch('updateSiteServerFeatures', _.merge(
                    this.$route.params, {
                        form: this.getFormData(this.$el)
                    })
                );
            }
        },
        computed : {
            siteId() {
                return this.$route.params.site_id
            },
            server() {
                if(this.serverId) {
                    return this.$store.state.serversStore.server;
                }
            },
            serverId() {
                return this.$route.params.server_id
            },
        }

    }
</script>