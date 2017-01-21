<template>
    <section>
        <p>
            Buoy Form
            <div class="jcf-form-wrap">

                <form @submit.prevent="saveBuoy">

                    <select>
                        <option></option>
                        <option v-for="buoyClass in buoyClasses">{{ buoyClass }}</option>
                    </select>

                    // TODO we will populate with information based on the class

                    <div class="jcf-input-group">
                        <input type="text" name="title" v-model="form.title">
                        <label for="title">
                            <span class="float-label">Title</span>
                        </label>
                    </div>

                    <div v-if="!image">
                        Icon
                        <template v-if="buoyId && buoy">
                            <img :src="buoy.icon">
                        </template>
                        <input type="file" @change="onFileChange">
                    </div>
                    <div v-else>
                        <img :src="image" />
                        <button @click="removeImage">Cancel</button>
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
                            <template v-if="buoyId">
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
            if(this.buoyId) {
                this.$store.dispatch('getBuoy', this.buoyId).then((buoy) => {
                    this.form.name = buoy.title
                })
            }

            this.$store.dispatch('getBuoyClasses')
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
                },
                image : null
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
                let form = this.form;

                if(this.buoyId) {
                    form = _.merge({ buoy : this.buoyId }, this.form)
                }

                let data = new FormData();

                _.each(form, function(value, key) {
                    if(value) {
                        data.append(key, value);
                    }
                })

                if(this.buoyId) {
                    this.$store.dispatch('updateBuoy', data)
                } else {
                    this.$store.dispatch('createBuoy', data)
                }
            },
            onFileChange(e) {
                const files = e.target.files || e.dataTransfer.files;
                if (!files.length) {
                    return;
                }

                const image = files[0];

                this.form.icon = image;

                this.createImage(image);
            },
            createImage(file) {
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.image = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            removeImage() {
                this.form.icon = null;
                this.image = null;
            }
        },
        computed: {
            buoyId() {
                return this.$route.params.buoy_id
            },
            buoy() {
                return this.$store.state.buoyAppsStore.buoy
            },
            buoyClasses() {
                return this.$store.state.buoyAppsStore.buoy_classes
            }
        }
    }
</script>