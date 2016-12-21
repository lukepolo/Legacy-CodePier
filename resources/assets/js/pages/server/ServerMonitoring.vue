<template>
    <section>
        <section id="middle" class="section-column" v-if="server">
            <template v-if="server.progress >= 100">
                <h4>Disk Usage</h4>
                <template v-if="server.stats && server.stats.disk_usage">
                    <p v-for="(stats, disk) in server.stats.disk_usage">
                        {{ disk }} : {{ stats.used }} / {{ stats.available }} ({{ stats.percent }})
                    </p>
                </template>
                <template v-else>
                    N/A
                </template>

                <h4>Memory</h4>
                <template v-if="server.stats && server.stats.memory">
                    <p v-for="(stats, memory_name) in server.stats.memory">
                        {{ memory_name }} : {{ stats.used }} / {{ stats.total }}
                    </p>
                </template>
                <template v-else>
                    N/A
                </template>

                <h4>CPU Load</h4>
                <template v-if="server.stats && server.stats.loads">
                    <p>1 / 5 / 10 mins for {{ server.stats.cpus }} CPUS</p>

                    <template v-for="(load, ago, index) in server.stats.loads">
                    <span>
                        {{ load }}%
                        <template v-if="index != (Object.keys(server.stats.loads).length - 1)">
                            /
                        </template>
                    </span>
                    </template>
                </template>
                <template v-else>
                    N/A
                </template>
            </template>
        </section>
    </section>
</template>

<script>
    export default {
        data() {
            return {}
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getServer', this.$route.params.server_id);
            }
        },
        computed: {
            server() {
                return this.$store.state.serversStore.server;
            }
        }
    }
</script>
