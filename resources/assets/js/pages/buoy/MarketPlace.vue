<template>
    <section>
        <p>
            Buoys

        <h2>Categories</h2>
        <p v-for="category in categories">
            {{ category.name }}
        </p>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="buoy in buoys">
                <td>
                    <template v-if="buoy.icon_url">
                        <img :src="buoy.icon_url" style="max-width:50px">
                    </template>
                    {{ buoy.title }}
                </td>
                <td>{{ buoy.description }}</td>

                <td>
                    Installs: {{ buoy.installs }}

                    <br>
                        <template v-if="serversHasBuoyApp(buoy.id)" v-for="server in serversHasBuoyApp(buoy.id)">
                            {{ getServer(server).name }} ({{ getServer(server).ip }})<br>
                        </template>
                    <br>
                </td>

                <td>
                   <a href="#" @click.prevent="install(buoy.id)">
                       Install
                   </a>
                </td>
                <td>
                    <template v-if="isAdmin()">
                        <router-link :to="{ name: 'buoy_edit', params : { buoy_id : buoy.id } }">Edit</router-link>
                    </template>
                </td>
            </tr>
            </tbody>
        </table>

        </p>
    </section>
</template>

<script>
    export default {
        created() {
            this.$store.dispatch('getBuoys')
            this.$store.dispatch('getCategories')
            this.$store.dispatch('getAllServers')
            this.$store.dispatch('allServerBuoys')
        },
        methods: {
            deleteCategory(buoy) {
                this.$store.dispatch('deleteBuoy', buoy)
            },
            install(buoyId) {
                this.$store.dispatch('getBuoy', buoyId)
            },
            getServer(server) {
                return _.find(this.servers, { id : parseInt(server) })
            },
            serversHasBuoyApp(buoyApp) {
                return _.map(this.allServerBuoys, (buoyApps, server) => {
                    if(_.indexOf(buoyApps, buoyApp) > -1) {
                        return server
                    }
                })
            },
        },
        computed: {
            buoys() {
                if(this.buoysPagination) {
                    return this.buoysPagination.data
                }
            },
            servers() {
                return this.$store.state.serversStore.all_servers
            },
            categories() {
                return this.$store.state.categoriesStore.categories
            },
            allServerBuoys() {
                return this.$store.state.serverBuoysStore.all_server_buoys
            },
            buoysPagination() {
                return this.$store.state.buoyAppsStore.buoy_apps
            },
        }
    }
</script>