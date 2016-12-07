<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">My Servers</h3>
            <div class="section-content">
                <div class="container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="server in servers">
                                <td>{{ server.name }}</td>
                                <td>{{ server.ip }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';

    export default {
        components: {
            LeftNav
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getServers');
            },
        },
        computed: {
            servers() {
                return this.$store.state.serversStore.servers;
            },
        }
    }
</script>
