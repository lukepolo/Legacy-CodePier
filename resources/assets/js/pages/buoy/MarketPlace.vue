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

                <td>Installs: {{ buoy.installs }}</td>

                <td>
                   <a href="#" @click.prevent="install(buoy.id)">Install</a>
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
        },
        methods: {
            deleteCategory(buoy) {
                this.$store.dispatch('deleteBuoy', buoy)
            },
            install(buoyId) {
                this.$store.dispatch('getBuoy', buoyId)
            }
        },
        computed: {
            buoys() {
                if(this.buoysPagination) {
                    return this.buoysPagination.data
                }
            },
            buoysPagination() {
                return this.$store.state.buoyAppsStore.buoys
            },
            categories() {
                return this.$store.state.categoriesStore.categories
            }
        }
    }
</script>