<template>
    <section>
        <div class="jcf-form-wrap">
            <div class="jcf-input-group">
                <div class="input-question">Select a server to install the buoy on</div>
                <div class="select-wrap">
                    <select>
                        <option></option>
                        <option v-for="server in servers" :value="server.id">
                            {{ server.name }} ({{ server.ip }})
                        </option>
                    </select>
                </div>
            </div>
        </div>

        // TODO
        Select a site or create new server for this buoy
    </section>
</template>

<script>
    export default {
        created() {
            this.$store.dispatch('getAllServers')
        },
        methods: {

        },
        computed: {
            servers() {
                return this.$store.state.serversStore.all_servers
            }
        }
    }
</script>