<template>
    <section>
        <p>
            Buoys
            <router-link :to="{ name: 'buoy_create' }"><span class="icon-server"></span>Create Buoy</router-link>
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
                <td>{{ buoy.name }}</td>
                <td>
                    <router-link :to="{ name: 'category_edit', params : { buoy : buoy.id } }"><span class="icon-server"></span>Edit</router-link>
                </td>
                <td>
                    <a @click.prevent="deleteBuoy(buoy.id)" href="#">Delete</a>
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