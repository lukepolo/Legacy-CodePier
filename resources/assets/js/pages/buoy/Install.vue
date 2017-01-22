<template>
    <section v-if="buoy">
        <template v-if="buoy && buoy.icon_url">
            <img :src="buoy.icon_url" style="max-width:100px">
        </template>
        {{ buoy.title }}
        {{ buoy.description }}

        <div class="jcf-form-wrap">

            <form @submit.prevent="installBuoy">

                <h3>Ports</h3>

                <template v-for="(port, port_description) in form.ports">

                    <div class="jcf-input-group">
                        <input type="text" name="ports[]" v-model="form.ports[port_description].local_port">
                        <label for="ports[]">
                            <span class="float-label">{{ port_description }} : Docker Port {{ port.docker_port }}</span>
                        </label>
                    </div>

                </template>

                <h3>Options </h3>

                <template v-for="(option, option_title) in form.options">

                    <div class="jcf-input-group">
                        <input type="text" name="optionValues[]" v-model="form.options[option_title].value">
                        <label for="optionValues[]">
                            <span class="float-label">{{ option_title }} : {{ option.description }}</span>
                        </label>
                    </div>

                </template>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Install Buoy</button>
                </div>

            </form>
    </section>
</template>

<script>
    export default {
        created() {
            this.$store.dispatch('getBuoy', this.buoyId).then((buoy) => {
                this.form.ports = buoy.ports;
                this.form.options = buoy.options;
            })
        },
        data() {
            return {
                form : {
                    ports : [],
                    options : [],
                }
            }
        },
        methods: {
            installBuoy() {
                alert('install buoy')
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