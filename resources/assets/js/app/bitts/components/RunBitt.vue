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
                      <div class="box--content">
                        <pre>{{ bitt.script }}</pre>
                      </div>
                    </div>
                    <label>Script</label>
                </div>

                <form @submit.prevent="runBitt">
                    <label>Select Servers to Install On</label>

                    <div class="flyform--group-checkbox" v-for="server in servers">
                        <label :class="{ disabled : form.global }">
                            <input :disabled="form.global" type="checkbox" name="servers" :value="server.id" v-model="form.servers">
                            <span class="icon"></span>{{ server.name }} ({{ server.ip }})
                        </label>
                    </div>

                    <div class="flyform--group-checkbox" v-if="isAdmin">
                        <label>
                            <input type="checkbox" name="global" v-model="form.global">
                            <span class="icon"></span>Global
                        </label>
                    </div>

                    <div class="btn-footer">
                        <button class="btn btn-primary" type="submit">Run Bitt</button>
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
      return this.$store.state.bitts.viewBitt;
    },
    servers() {
      return this.$store.state.user_servers.servers;
    }
  }
};
</script>
