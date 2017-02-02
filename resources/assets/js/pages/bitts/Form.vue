<template>
    <section>
        <p>
            Bitt Form
        <div class="jcf-form-wrap">

            <form @submit.prevent="saveUpdateBitt">

                <div class="jcf-input-group">
                    <input type="text" name="title" v-model="form.title">
                    <label for="title">
                        <span class="float-label">Title</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Category</div>
                    <div class="select-wrap">
                        <select name="category" v-model="form.category">
                            <option></option>
                            <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Systems</div>
                    <div class="select-wrap">
                        <select multiple name="category" v-model="form.systems">
                            <option></option>
                            <option v-for="system in systems" :value="system.id">{{ system.name }}</option>
                        </select>
                    </div>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Description</div>
                    <textarea name="description" v-model="form.description"></textarea>
                </div>

                <div class="jcf-input-group">
                    <div class="input-question">Script</div>
                    <textarea name="script" v-model="form.script"></textarea>
                </div>

                <!--<div class="jcf-input-group input-checkbox">-->
                    <!--<div class="input-question">Active</div>-->
                    <!--<label>-->
                        <!--<input type="checkbox" name="active" v-model="form.active">-->
                        <!--<span class="icon"></span>-->
                        <!--Verified-->
                    <!--</label>-->
                <!--</div>-->

                <!--<div class="jcf-input-group input-checkbox">-->
                    <!--<div class="input-question">Active</div>-->
                    <!--<label>-->
                        <!--<input type="checkbox" name="active" v-model="form.active">-->
                        <!--<span class="icon"></span>-->
                        <!--Verified-->
                    <!--</label>-->
                <!--</div>-->

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">
                        <template v-if="bittId">
                            Update
                        </template>
                        <template v-else>
                            Create
                        </template>
                        Bitt
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
            this.$store.dispatch('getCategories').then(() => {
                    this.$store.dispatch('getSystems').then(() => {
//                this.$store.dispatch('getBuoy', this.buoyAppId).then((buoy) => {
//                    this.form.icon = buoy.icon
//                    this.form.ports = buoy.ports
//                    this.form.title = buoy.title
//                    this.form.active = buoy.active
//                    this.form.options = buoy.options
//                    this.form.buoy_class = buoy.buoy_class
//                    this.form.description = buoy.description
//                    this.form.category = buoy.categories[0].id
//                    })
                })
            })
        },
        data() {
            return {
                form : {
                    title : null,
                    systems : [],
                    category : null,
                    description : null,
                },
            }
        },
        methods: {
            saveUpdateBitt() {
                this.$store.dispatch('createBitt', this.form);
            },
        },
        computed: {
            systems() {
                return this.$store.state.systemsStore.systems
            },
            categories() {
                return this.$store.state.categoriesStore.categories
            },
        }
    }
</script>