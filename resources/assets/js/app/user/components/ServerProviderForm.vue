<template>
    <section>

        <template v-if="adding">

            <form @submit.prevent="connectProvider">

                <div class="flyform--group">
                    <input type="text" v-model="form.token" placeholder=" ">
                    <label>Api Token</label>
                </div>

                <div class="flyform--group" v-if="provider.secret_token">
                    <input type="text" v-model="form.secret_token" placeholder=" ">
                    <label>Secret Token</label>
                </div>
                <br>
                <div class="providers--item-footer">
                    <div class="flyform--footer-btns">
                        <span class="btn" @click="adding = false">Cancel</span>
                        <button class="btn btn-primary">Connect</button>
                    </div>
                </div>




            </form>

        </template>

        <a class="btn btn-default" @click="adding = true" v-if="!adding">
            {{ provider.name }}
        </a>

    </section>
</template>


<script>
    export default {
        props: ['provider'],
        data() {
            return {
                adding : false,
                form : this.createForm({
                    token : null,
                    secret_token : null
                })
            }
        },
        methods : {
            connectProvider() {
                this.form.post('/api/server/providers/'+this.provider.provider_name+'/provider ').then(() => {
                    this.$store.dispatch('user_server_providers/get', this.$store.state.user.user.id);
                })
            }
        }
    }
</script>