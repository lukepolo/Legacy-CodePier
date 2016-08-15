<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column" v-if="server">
            <server-nav :server="server"></server-nav>
            <span id="cron-preview"></span>
            <form v-on:submit.prevent="onSubmit">
                <input type="hidden" name="server_id" :value="server.id">
                <input type="text" name="cron">
                <input type="hidden" name="cron_timing">
                <select name="user">
                    <option value="root">Root User</option>
                    <option value="codepier">CodePier User</option>
                </select>
                <div id="cron-maker"></div>
                <button type="submit">Create Cron</button>
            </form>

            <table class="table" v-if="cron_jobs" v-for="cron_job in cron_jobs">
                <thead>
                <tr>
                    <th>Job</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{ cron_job.job }}</td>
                    <td><a v-on:click="deleteCronJob(cron_job.id)" href="#" class="fa fa-remove">X</a></td>
                </tr>
                </tbody>
            </table>
        </section>
    </section>
</template>

<script>
    import ServerNav from './components/ServerNav.vue';
    import LeftNav from './../../core/LeftNav.vue';

    export default {
        components : {
            LeftNav,
            ServerNav
        },
        data() {
            return {
                server : null,
                cron_jobs : [],
            }
        },
        methods : {
            onSubmit() {
                Vue.http.post(this.action('Server\Features\ServerCronJobController@store'), this.getFormData($(this.$el))).then((response) => {
                    this.cron_jobs.push(response.json());
                }, (errors) => {
                    alert(error);
                });
            },
            getCronJobs() {
                Vue.http.get(this.action('Server\Features\ServerCronJobController@index', {server_id : this.$route.params.server_id})).then((response) => {
                    this.cron_jobs = response.json();
                }, (errors) => {
                    alert(error);
                });
            },
            deleteCronJob(cron_job_id) {
                Vue.http.delete(this.action('Server\Features\ServerCronJobController@destroy', {cron_job : cron_job_id})).then((response) => {
                    this.getCronJobs();
                }, (errors) => {
                    alert(error);
                });
            }

        },
        mounted() {
            Vue.http.get(this.action('Server\ServerController@show', {server : this.$route.params.server_id})).then((response) => {
                this.server = response.json();
            }, (errors) => {
                alert(error);
            });

            this.getCronJobs();

            $('#cron-maker').cron({
                onChange: function() {
                    var cronTiming = $(this).cron("value");
                    $('#cron-preview').text(cronTiming);
                    $('input[name="cron_timing"]').val(cronTiming);
                }
            });
        }
    }
</script>
