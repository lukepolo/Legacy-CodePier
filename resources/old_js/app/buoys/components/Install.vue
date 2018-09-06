<template>
  <div class="column--content" v-if="buoyApp">
    <div class="side-container">
      <template v-if="buoyApp && buoyApp.icon_url">
        <img :src="buoyApp.icon_url" style="max-width:100px">
      </template>

      <h3>{{ buoyApp.title }}</h3>
      <form @submit.prevent="installBuoy">
        <template v-for="(port, port_description) in form.ports">
          <div class="flyform--group">
            <input type="text" name="ports[]" v-model="form.ports[port_description].local_port">
            <label for="ports[]">
                {{ port_description }} : Docker Port {{ port.docker_port }}
            </label>
          </div>
        </template>

        <template v-for="(option, option_title) in form.options">
          <div class="flyform--group">
            <input type="text" name="optionValues[]" v-model="form.options[option_title].value">
            <label for="optionValues[]">
                {{ option_title }} : {{ option.description }}
            </label>
          </div>
        </template>

        <div class="flyform--group">
          <label>Select Server to Install On</label>
          <div class="flyform--group-select">
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
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: this.createForm({
        ports: [],
        options: [],
        server: null,
        buoy_app_id: null,
      }),
    };
  },
  methods: {
    installBuoy() {
      this.form.buoy_app_id = this.buoyApp.id;
      return this.$store.dispatch("buoys/installOnServer", this.form);
    },
  },
  computed: {
    buoyApp() {
      let buoyApp = this.$store.state.buoys.buoy_app;

      if (buoyApp) {
        this.form.ports = buoyApp.ports;
        this.form.options = buoyApp.options;
        return buoyApp;
      }
    },
    servers() {
      return _.filter(this.$store.state.user_servers.servers, function(server) {
        return server.progress >= 100;
      });
    },
  },
};
</script>
