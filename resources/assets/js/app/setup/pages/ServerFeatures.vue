<template>
    <section>
        <template v-if="!server && update !==  false">
            <form @submit.prevent="dispatchMethod" enctype="multipart/form-data" class="floating-labels">

                <features></features>

                <div class="flyform--footer">
                    <div class="flyform--footer-btns">
                        <button class="btn btn-primary" type="submit">Update Site Server Features</button>
                    </div>
                </div>
            </form>
        </template>
        
        <template v-else>

            <features></features>

        </template>
    </section>
</template>

<script>
    import Features from '../components/Features'

    export default {
        props : ["update"],
        components: {
            Features
        },
        methods : {
            dispatchMethod() {
                this.$store.dispatch('user_site_server_features/update', _.merge(
                    this.$route.params, {
                        formData : this.getFormData(this.$el)
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
                    return this.$store.state.user_servers.server
                }
            },
            serverId() {
                return this.$route.params.server_id
            }
        }

    }
</script>