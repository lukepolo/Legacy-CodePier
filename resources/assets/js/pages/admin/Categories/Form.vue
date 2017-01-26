<template>
    <section>
        <p>
            Categories Form
            <div class="jcf-form-wrap">

                <form @submit.prevent="saveCategory">

                    <div class="jcf-input-group">
                        <input type="text" name="name" v-model="form.name">
                        <label for="name">
                            <span class="float-label">Category Name</span>
                        </label>
                    </div>

                    <div class="btn-footer">
                        <button class="btn btn-primary" type="submit">
                            <template v-if="categoryId">
                                Update
                            </template>
                            <template v-else>
                                Create
                            </template>
                            Category
                        </button>
                    </div>

                </form>

            </div>

        </p>
    </section>
</template>

<script>
    export default {
        created() {
            if(this.categoryId) {
                this.$store.dispatch('getCategory', this.categoryId).then((category) => {
                    this.form.name = category.name
                })
            }
        },
        data() {
          return {
              form : {
                  name : null
              }
          }
        },
        methods: {
            saveCategory() {
                if(this.categoryId) {
                    let data = this.form;
                    data.category = this.categoryId
                    this.$store.dispatch('updateCategory', data)
                } else {
                    this.$store.dispatch('createCategory', this.form)
                }
            }
        },
        computed: {
            categoryId() {
                return this.$route.params.category_id
            },
            category() {
                return this.$store.state.categoriesStore.category
            }
        }
    }
</script>