<template>
    <section>
        <h3>Buoys</h3>

        <!--<h2>Categories</h2>-->
        <!--<p v-for="category in categories">-->
            <!--{{ category.name }}-->
        <!--</p>-->
        <div class="group-container">
            <buoy-app :buoyApp="buoyApp" v-for="buoyApp in buoyApps" :key="buoyApp.id"></buoy-app>
        </div>

    </section>
</template>

<script>
    import BuoyApp from '../../components/BuoyApp.vue'
    export default {
        components : {
          BuoyApp
        },
        created() {
            this.$store.dispatch('getBuoys')
            this.$store.dispatch('getCategories')
            this.$store.dispatch('getAllServers')
            this.$store.dispatch('allServerBuoys')
        },
        computed: {
            buoyApps() {
                if(this.buoyAppsPagination) {
                    return this.buoyAppsPagination.data
                }
            },
            categories() {
                return this.$store.state.categoriesStore.categories
            },
            buoyAppsPagination() {
                return this.$store.state.buoyAppsStore.buoy_apps
            },
        }
    }
</script>