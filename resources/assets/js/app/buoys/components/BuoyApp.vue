<template>
    <div class="group--item">
        <div class="group--item-heading">
            <h4>
                <div class="group--item-heading-name">{{ buoyApp.title }}</div>
                <div class="action-btn">
                    <template v-if="isAdmin">
                        <router-link :to="{ name: 'buoy_edit', params : { buoy_id : buoyApp.id } }" class="btn btn-small"><span class="icon-pencil"></span></router-link>
                    </template>
                </div>
            </h4>
        </div>

        <div class="group--item-subheading">
            <div class="group--item-img">
                <template v-if="buoyApp.icon_url">
                    <img :src="buoyApp.icon_url">
                </template>
                <template v-else>
                    <img src="/assets/img/icons/buoy.svg">
                </template>
            </div>
            <div class="group--item-subheading-info">
                <template v-for="category in buoyApp.categories">
                    <span class="label label-primary">{{ category.name }}</span>
                </template>
            </div>
            <div class="group--item-subheading-info">
                {{ buoyApp.installs }} Installs
            </div>
        </div>

        <div class="group--item-content">

            {{ buoyApp.description }}

            <p>
                <template v-for="server in serversHasBuoyApp">
                    {{ getServer(server, 'name') }} ({{ getServer(server, 'ip') }})<br>
                </template>
            </p>

        </div>

        <div class="btn-footer text-center" v-if="servers.length">
            <button class="btn btn-primary" @click.prevent="install">
                Install
            </button>
        </div>
        <div v-else>
            Please create a server before attaching a buoy.
        </div>
    </div>
</template>

<script>
    export default {
        props: ['buoyApp'],
        methods : {
            install() {
                this.$store.dispatch('buoys/show', this.buoyApp.id)
            }
        },
        computed : {
            allServerBuoys() {
                return this.$store.state.user_server_buoys.all
            },
            serversHasBuoyApp() {
                return _.omitBy(_.map(this.allServerBuoys, (serverBuoyApps, server) => {
                    if((_.indexOf(serverBuoyApps, this.buoyApp.id) >= 0)) {
                        return server
                    }
                }), _.isEmpty)
            },
            servers() {
                return _.filter(this.$store.state.user_servers.servers, function(server) {
                    return server.progress >= 100
                })
            }
        }
    }
</script>