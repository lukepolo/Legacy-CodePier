<template>
    <section>
        <tr>
            <td class="break-word">{{ cronJob.job }}</td>
            <td>{{ cronJob.user }}</td>
            <td>
                <template v-if="isRunningCommand">
                    {{ isRunningCommand.status }}
                </template>
            </td>

            <td class="table--action">

                <server-selection :servers.sync="form.servers" :server_types.sync="form.server_types"></server-selection>

                <div class="btn btn-success" v-if="form.diff().length" @click="updateCronJob(cronJob)">update</div>


                <tooltip message="Delete">
                    <span class="table--action-delete">
                        <a @click="deleteCronJob"><span class="icon-trash"></span></a>
                    </span>
                </tooltip>
            </td>
        </tr>
    </section>
</template>

<script>

    import ServerSelection from "./ServerSelection.vue"

    export default {
        props : ['cronJob'],
        components : {
            ServerSelection
        },
        data() {
            return {
                form : this.createForm({
                    cron_job : this.cronJob.id,
                    servers : this.cronJob.servers,
                    site : this.$route.params.site_id,
                    server_types : this.cronJob.server_types,
                }),
            }
        },
        methods: {
            updateCronJob() {
                this.$store.dispatch('user_site_cron_jobs/patch', this.form)
            },
            deleteCronJob() {
                if(this.siteId) {
                    this.$store.dispatch('user_site_cron_jobs/destroy', {
                        site : this.siteId,
                        cron_job : this.cronJob.id

                    });
                }
                if(this.serverId) {
                    this.$store.dispatch('user_server_cron_jobs/destroy', {
                        server : this.serverId,
                        cron_job : this.cronJob.id,
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
                return this.isCommandRunning('App\\Models\\CronJob', this.cronJob.id)
            },
        }
    }
</script>