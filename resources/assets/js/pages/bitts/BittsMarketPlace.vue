<template>
    <section>
        <p>
            Bitts
            <router-link :to="{ name: 'bitt_create' }">Create Bitt</router-link>

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
                </tr>
            </thead>
            <tbody>
                <tr v-for="bitt in bitts">
                    <td>
                        {{ bitt.title }}

                        (
                        <template v-for="category in bitt.categories">
                            {{ category.name }}
                        </template>
                        )

                    </td>
                    <td>{{ bitt.description }}</td>

                    <td>
                        <a href="#" @click.prevent="getBitt(bitt.id)">
                            Run On Server
                        </a>
                    </td>
                    <td>
                        <router-link :to="{ name: 'bitt_edit', params : { bitt_id : bitt.id} }">
                            Edit
                        </router-link>
                        <a href="#" @click.prevent="deleteBitt(bitt)">Delete</a>
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
            this.$store.dispatch('getCategories').then(() => {
                this.$store.dispatch('getBitts')
            })
        },
        methods : {
            getBitt(bitt) {
                this.$store.dispatch('getBitt', bitt)
            },
            deleteBitt(bitt) {
                this.$store.dispatch('deleteBitt', bitt.id)
            }
        },
        computed : {
            bitts() {
                return this.$store.state.bittsStore.bitts.data
            },
            pagination() {
                return this.$store.state.bittsStore.bitts
            },
            categories() {
                return this.$store.state.categoriesStore.categories
            },
        }
    }
</script>