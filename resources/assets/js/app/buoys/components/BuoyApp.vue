<template>
    <div class="group">
        <div class="group-heading">
            <h4>
                <template v-if="buoyApp.icon_url">
                    <img :src="buoyApp.icon_url" style="max-width:50px">
                </template>

                {{ buoyApp.title }}

                <p class="pull-right">
                    {{ buoyApp.installs }} Installs
                    <br>
                    <template v-if="isAdmin()">
                        <router-link :to="{ name: 'buoy_edit', params : { buoy_id : buoyApp.id } }">Edit</router-link>
                    </template>
                </p>

                <div class="small">
                    <template v-for="category in buoyApp.categories">
                        <span class="label label-primary">{{ category.name }}</span>
                    </template>
                </div>

            </h4>
        </div>

        <div class="group-content">

            {{ buoyApp.description }}

            <p>
                <template v-for="server in serversHasBuoyApp">
                    {{ getServer(server, 'name') }} ({{ getServer(server, 'ip') }})<br>
                </template>
            </p>

        </div>

        <div class="btn-footer text-center">
            <button class="btn btn-primary" @click.prevent="install">
                Install
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['buoyApp'],
        methods : {
            install() {
                this.$store.dispatch('getBuoy', this.buoyApp.id)
            }
        },
        computed : {
            allServerBuoys() {
                return this.$store.state.serverBuoysStore.all_server_buoys
            },
            serversHasBuoyApp() {
                return _.omitBy(_.map(this.allServerBuoys, (serverBuoyApps, server) => {
                    if((_.indexOf(serverBuoyApps, this.buoyApp.id) >= 0)) {
                        return server
                    }
                }), _.isEmpty)
            }
        }
    }
</script>