<template>
    <section>
        <p>
            Categories
            <router-link :to="{ name: 'category_create' }"><span class="icon-server"></span>Create Category</router-link>

            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="category in categories">
                        <td>{{ category.name }}</td>
                        <td>
                            <router-link :to="{ name: 'category_edit', params : { category_id : category.id } }"><span class="icon-server"></span>Edit</router-link>
                        </td>
                        <td>
                           <a @click.prevent="deleteCategory(category.id)" href="#">Delete</a>
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
            this.$store.dispatch('getCategories')
        },
        methods: {
            deleteCategory(categoryId) {
                this.$store.dispatch('deleteCategory', categoryId)
            }
        },
        computed: {
            categories() {
                return this.$store.state.categoriesStore.categories
            }
        }
    }
</script>