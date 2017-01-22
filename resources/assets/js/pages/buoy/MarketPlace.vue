<template>
    <section>
        <p>
            Buoys
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="buoy in buoys">
                <td>{{ buoy.title }}</td>
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
        },
        methods: {
            deleteCategory(buoy) {
                this.$store.dispatch('deleteBuoy', buoy)
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
            }
        }
    }
</script>