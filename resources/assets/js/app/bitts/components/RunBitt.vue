<template>
    <section v-if="bitt">

        title
        {{ bitt.title }}
        <br>
        description
        {{ bitt.description }}
        <br>
        official
        {{ bitt.official }}
        <br>
        Verified
        {{ bitt.verified }}
        <br>
        <pre>
            {{ bitt.script }}
        </pre>
        <br>
        <div class="jcf-form-wrap">

            <form @submit.prevent="runBitt">

                <div class="input-question">Select servers to install on</div>
                <div>
                    <select multiple name="server" v-model="form.servers">
                        <option></option>
                        <option v-for="server in servers" :value="server.id">{{ server.name }} ({{ server.ip }})</option>
                    </select>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Run Bitt</button>
                </div>

            </form>
        </div>
    </section>
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
