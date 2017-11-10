<template>
    <section v-if="buoyApp">
        <template v-if="buoyApp && buoyApp.icon_url">
            <img :src="buoyApp.icon_url" style="max-width:100px">
        </template>
        {{ buoyApp.title }}

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

                <div class="jcf-input-group" v-if="!form.new_server">
                    <div class="input-question">Select server to install on</div>
                    <div>
                        <select name="server" v-model="form.server">
                            <option></option>
                            <option v-for="server in servers" :value="server.id">{{ server.name }} ({{ server.ip }})</option>
                        </select>
                    </div>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Install Buoy</button>
                </div>

            </form>
        </div>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                form : this.createForm({
                    ports : [],
                    options : [],
                    server : null,
                    buoy_app_id : null,
                })
            }
        },
        methods: {
            installBuoy() {
                this.form.buoy_app_id = this.buoy_app.id
                return this.$store.dispatch('buoys/installOnServer', this.form)
            }
        },
        computed: {
            buoyApp() {
                let buoy_app = this.$store.state.buoys.buoy_app

                if(buoy_app) {
                    this.form.ports = buoy_app.ports
                    this.form.options = buoy_app.options
                    return buoy_app
                }
            },
            servers() {
                return _.filter(this.$store.state.user_servers.servers, function(server) {
                    return server.progress >= 100
                })
            }
        }
    }
</script>