<template>
    <section>
        <tr>
            <td class="break-word">{{ daemon.command }}</td>
            <td>{{ daemon.user }}</td>
            <td>
                <template v-if="isRunningCommand">
                    {{ isRunningCommand.status }}
                </template>
            </td>

            <td class="table--action">

                <server-selection :server_ids.sync="form.server_ids" :server_types.sync="form.server_types"></server-selection>

                <div class="btn btn-success" v-if="form.diff().length" @click="updateDaemon(daemon)">update</div>


                <tooltip message="Delete">
                    <span class="table--action-delete">
                        <a @click="deleteDaemon"><span class="icon-trash"></span></a>
                    </span>
                </tooltip>
            </td>
        </tr>
    </section>
</template>

<script>

    import ServerSelection from "./ServerSelection.vue"

    export default {
        props : ['daemon'],
        components : {
            ServerSelection
        },
        data() {
            return {
                form : this.createForm({
                    daemon : this.daemon.id,
                    site : this.$route.params.site_id,
                    server_ids : this.daemon.server_ids,
                    server_types : this.daemon.server_types,
                }),
            }
        },
        methods: {
            updateDaemon() {
                this.$store.dispatch('user_site_daemons/patch', this.form)
            },
            deleteDaemon: function () {

                if(this.siteId) {
                    this.$store.dispatch('user_site_daemons/destroy', {
                        site: this.siteId,
                        daemon: this.daemon.id,
                    });
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_daemons/destroy', {
                        server: this.serverId,
                        daemon: this.daemon.id,
                    });
                }
            },
        },
        computed : {
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            isRunningCommand() {
                return this.isCommandRunning('App\\Models\\Daemon', this.daemon.id)
            },
        }
    }
</script>