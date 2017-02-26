<template>
    <section v-if="bitt">

        title
        {{ bitt.title }}
        <br>
        description
        {{ bitt.description }}
        <br>
        official
        {{ bitt.official }}
        <br>
        Verified
        {{ bitt.verified }}
        <br>
        <pre>
            {{ bitt.script }}
        </pre>
        <br>
        <div class="jcf-form-wrap">

            <form @submit.prevent="runBitt">

                <div class="jcf-input-group" v-if="!form.new_server">
                    <div class="input-question">Select server to install on</div>
                    <div>
                        <select multiple name="server" v-model="form.servers">
                            <option></option>
                            <option v-for="server in servers" :value="server.id">{{ server.name }} ({{ server.ip }})</option>
                        </select>
                    </div>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Install Bitt</button>
                </div>

            </form>
        </div>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                form : {
                    bitt : null,
                    servers : [],
                }
            }
        },
        methods: {
            runBitt() {
                this.form.bitt_id = this.bitt.id
                this.$store.dispatch('runBittOnServers', this.form)
            }
        },
        computed: {
            bitt() {
                let bitt = this.$store.state.bittsStore.bitt

                if(bitt) {
                    this.form.bitt = bitt.id
                    return bitt
                }
            },
            servers() {
                return this.$store.state.serversStore.provisioned_servers
            }
        }
    }
</script>