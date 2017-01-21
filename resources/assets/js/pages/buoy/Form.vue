<template>
    <section>
        <p>
            Buoy Form
            <div class="jcf-form-wrap">

                <form @submit.prevent="saveBuoy">

                    <div class="jcf-input-group">
                        <input type="text" name="class" v-model="form.buoy_class">
                        <label for="buoy_class">
                            <span class="float-label">Buoy Class</span>
                        </label>
                    </div>

                    // TODO we will populate with information based on the class

                    <div class="jcf-input-group">
                        <input type="text" name="title" v-model="form.title">
                        <label for="title">
                            <span class="float-label">Title</span>
                        </label>
                    </div>

                    // TODO - file upload
                    <div class="jcf-input-group">
                        <input type="text" name="icon" v-model="form.icon">
                        <label for="icon">
                            <span class="float-label">Icon</span>
                        </label>
                    </div>

                    <div class="jcf-input-group">
                        <textarea name="description" v-model="form.description"></textarea>
                        <label for="description">
                            <span class="float-label">Description</span>
                        </label>
                    </div>

                    <h3>Options </h3>

                    <a href="#" @click.prevent="addOption">Add Option</a>
                    <template v-for="(option, index) in form.options">

                        <div class="jcf-input-group">
                            <input type="text" name="options[]" v-model="form.options[index].option">
                            <label for="title">
                                <span class="float-label">Option</span>
                            </label>
                        </div>

                        <div class="jcf-input-group">
                            <input type="text" name="optionValues[]" v-model="form.options[index].value">
                            <label for="optionValues[]">
                                <span class="float-label">Value</span>
                            </label>
                        </div>

                    </template>

                    <div class="jcf-input-group input-checkbox">
                        <div class="input-question">Active</div>
                        <label>
                            <input type="checkbox" name="active" v-model="form.active">
                            <span class="icon"></span>
                            Active
                        </label>
                    </div>

                    <div class="jcf-input-group">
                        <input type="number" name="title" v-model="form.local_port">
                        <label for="local_port">
                            <span class="float-label">Local Port</span>
                        </label>
                    </div>

                    <div class="jcf-input-group">
                        <input type="number" name="title" v-model="form.docker_port">
                        <label for="docker_port">
                            <span class="float-label">Docker Port</span>
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
                            Buoy
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
                this.$store.dispatch('getBuoy', this.buoyId).then((buoy) => {
                    this.form.name = buoy.title
                })
            }
        },
        data() {
            return {
                form : {
                    icon : null,
                    title : null,
                    options : [],
                    active : false,
                    local_port: null,
                    buoy_class : null,
                    docker_port : null,
                    description : null,
                }
            }
        },
        methods: {
            addOption() {
                this.form.options.push({
                    'option': 'New Option',
                    'value' : ''
                })
            },
            saveBuoy() {
                if(this.categoryId) {
                    let data = this.form;
                    data.buoy = this.buoyId
                    this.$store.dispatch('updateBuoy', data)
                } else {
                    this.$store.dispatch('createBuoy', this.form)
                }
            }
        },
        computed: {
            buoyId() {
                return this.$route.params.buoy_id
            },
            buoy() {
                return this.$store.state.buoyAppsStore.buoy
            }
        }
    }
</script>