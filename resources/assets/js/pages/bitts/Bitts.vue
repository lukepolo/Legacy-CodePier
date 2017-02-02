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
                        <a href="#" @click.prevent="install(bitt.id)">
                            Install
                        </a>
                    </td>
                    <td>
                        <router-link :to="{ name: 'bitt_edit', params : { bitt_id : bitt.id } }">Edit</router-link>
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
            getCategory(bitt) {
            }
        },
        computed : {
            bitts() {
                return this.$store.state.bittsStore.bitts.data
            },
            categories() {
                return this.$store.state.categoriesStore.categories
            },
            pagination() {
                return this.$store.state.bittsStore.bitts
            }
        }
    }
</script>