<template>
  <section>
    <div class="flex flex--center">
      <h3 class="flex--grow">Firewall Rules</h3>

      <tooltip message="Add Firewall Rule">
        <span
          class="btn btn-small btn-primary"
          :class="{ 'btn-disabled': this.showForm }"
          @click="showForm = true"
        >
          <span class="icon-plus"></span>
        </span>
      </tooltip>
    </div>
    <table class="table" v-if="firewallRules.length">
      <thead>
        <tr>
          <th>Name</th>
          <th>Port</th>
          <th>From IP</th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="firewallRule in firewallRules">
          <td>{{ firewallRule.description }}</td>
          <td>{{ firewallRule.port }} {{ firewallRule.type }}</td>
          <td>{{ firewallRule.from_ip }}</td>
          <td>
            <!--<template v-if="isRunningCommandFor(firewallRule.id)">-->
            <!--{{ isRunningCommandFor(firewallRule.id).status }}-->
            <!--</template>-->
          </td>
          <td class="table--action">
            <tooltip message="Delete">
              <span class="table--action-delete">
                <a @click.prevent="deleteFirewallRule(firewallRule.id)"
                  ><span class="icon-trash"></span
                ></a>
              </span>
            </tooltip>
          </td>
        </tr>
      </tbody>
    </table>

    <base-form v-form="form" :action="createFirewallRule" v-if="showForm">
      <base-input
        validate
        v-model="form.description"
        name="description"
        label="Description"
      ></base-input>

      <div class="grid-2">
        <base-input
          validate
          v-model="form.port"
          name="port"
          label="Port"
        ></base-input>

        <div class="flyform--group">
          <label>Select Type</label>
          <div class="flyform--group-select">
            <select v-model="form.type" name="type">
              <option value="tcp">TCP</option>
              <option value="udp">UDP</option>
            </select>
          </div>
        </div>
      </div>

      <base-input
        validate
        v-model="form.from_ip"
        name="from_ip"
        label="From IP"
      ></base-input>

      <template slot="buttons">
        <span class="btn" @click.prevent="cancel">Cancel</span>
        <button class="btn btn-primary" type="submit">Add Firewall Rule</button>
      </template>
    </base-form>
  </section>
</template>

<script>
export default {
  data() {
    return {
      showForm: false,
      form: this.createForm({
        port: null,
        type: null,
        from_ip: null,
        description: null,
      }).validation({
        rules: {
          port: "required|max:6",
          type: "required",
          description: "required|max:255",
        },
      }),
    };
  },
  watch: {
    $route: {
      immediate: true,
      handler: "fetchData",
    },
    firewallRules: {
      handler() {
        if (!this.firewallRules.length) {
          this.showForm = true;
        }
      },
    },
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user/sites/firewallRules/get", {
        site: this.$route.params.site,
      });
    },
    createFirewallRule() {
      this.$store
        .dispatch("user/sites/firewallRules/create", {
          data: this.form.data(),
          parameters: {
            site: this.$route.params.site,
          },
        })
        .then(() => {
          this.cancel();
        });
    },
    deleteFirewallRule(firewallRuleId) {
      this.$store.dispatch("user/sites/firewallRules/destroy", {
        firewall_rule: firewallRuleId,
        site: this.$route.params.site,
      });
    },
    cancel() {
      this.form.reset();
      this.showForm = false;
    },
  },
  computed: {
    firewallRules() {
      return this.$store.state.user.sites.firewallRules.firewall_rules;
    },
  },
};
</script>
