<template>
    <section>
        <!--<h2>Categories</h2>-->
        <!--<p v-for="category in categories">-->
            <!--{{ category.name }}-->
        <!--</p>-->
        <div class="group">
            <buoy-app :buoyApp="buoyApp" v-for="buoyApp in buoyApps" :key="buoyApp.id"></buoy-app>
        </div>

    </section>
</template>

<script>
    import {
        BuoyApp
    } from '../components'

    export default {
        components : {
          BuoyApp
        },
        created() {
            this.$store.dispatch('buoys/get')
            this.$store.dispatch('admin_categories/get')
//            this.$store.dispatch('getAllServers')
            this.$store.dispatch('user_server_buoys/all')
        },
        computed: {
            buoyApps() {
                if(this.buoyAppsPagination) {
                    return this.buoyAppsPagination.data
                }
            },
            categories() {
                return this.$store.state.admin_categories.categories
            },
            buoyAppsPagination() {
                return this.$store.state.buoys.buoy_apps
            },
        }
    }
</script>