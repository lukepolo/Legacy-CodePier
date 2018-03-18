<template>
        <div class="section-content">
            <div class="side-container">
                <h3>{{ bitt.title }}</h3>

                <div class="flyform--group">
                    {{ bitt.description }}
                    <label for="description">Description</label>
                </div>

                <p class="text-badge text-success" v-if="bitt.official">
                    <span class="icon-verified"></span> Official
                </p>
                <p class="text-badge text-success" v-if="bitt.verified">
                    <span class="icon-verified"></span> Verified
                </p>

                <div class="flyform--group">
                    <div class="box">
                        <pre>{{ bitt.script }}
                        </pre>
                    </div>
                    <label>Script</label>
                </div>

                <div class="flyform--group">
                    <form @submit.prevent="runBitt">
                        <select multiple name="server" v-model="form.servers">
                            <option></option>
                            <option v-for="server in servers" :value="server.id">{{ server.name }} ({{ server.ip }})</option>
                        </select>
                        <label>Select Servers to Install On</label>
                    </form>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Run Bitt</button>
                </div>
            </div>
        </div>
</template>

<script>
export default {
  data() {
    return {
      form: this.createForm({
        bitt: null,
        servers: []
      })
    };
  },
  methods: {
    runBitt() {
      this.form.bitt = this.bitt.id;
      this.$store.dispatch("bitts/run", this.form);
    }
  },
  computed: {
    bitt() {
      return this.$store.state.bitts.bitt;
    },
    servers() {
      return this.$store.state.user_servers.servers;
    }
  }
};
</script>
