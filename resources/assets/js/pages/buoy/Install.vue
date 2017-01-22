<template>
    <section v-if="buoy">
        <template v-if="buoy && buoy.icon_url">
            <img :src="buoy.icon_url" style="max-width:100px">
        </template>
        {{ buoy.title }}

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

                // Currently we only support adding to previously setup server
                <div class="jcf-input-group">
                    <div class="input-question">Select server to install on</div>
                    <div>
                        <select>
                            <option></option>
                            <option v-for="server in servers">{{ server.name }} ({{ server.ip }})</option>
                        </select>
                    </div>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Install Buoy</button>
                </div>

            </form>
    </section>
</template>

<script>
    export default {
        created() {
            this.$store.dispatch('getAllServers')
        },
        data() {
            return {
                form : {
                    ports : [],
                    options : [],
                    server : null
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

                let buoy = this.$store.state.buoyAppsStore.buoy

                if(buoy) {
                    this.form.ports = buoy.ports
                    this.form.options = buoy.options
                    return buoy
                }
            },
            servers() {
                return this.$store.state.serversStore.all_servers
            }
        }
    }
</script>