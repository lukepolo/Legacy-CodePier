<template>
    <div>

        <div class="flex flex--center">
            <h3 class="flex--grow">
                Private IP Addresses
            </h3>

            <tooltip message="Add Environment Variable">
                <span class="btn btn-small btn-primary" :class="{ 'btn-disabled' : this.shouldShowForm }" @click="showForm = true">
                    <span class="icon-plus"></span>
                </span>
            </tooltip>
        </div>

        <p>
            CodePier uses Private ip addresses to help facilitate load balancers, firewall rules from
            Database servers , and worker servers.
        </p>

        <div class="alert alert-info">
            If a private IP address is changed, the server will automatically run <code><strong>Fix Server Configuration</strong></code> from the help menu.
        </div>

        <table class="table" v-if="privateIpAddresses.length">
            <thead>
            <tr>
                <th>IP Address</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="privateIpAddress in privateIpAddresses">
                <td>{{ privateIpAddress }}</td>
                <td class="table--action">
                    <tooltip message="Delete">
                        <span class="table--action-delete">
                            <a @click="deleteIp(privateIpAddress)"><span class="icon-trash"></span></a>
                        </span>
                    </tooltip>
                </td>
            </tr>
            </tbody>
        </table>
        <!--10.132.27.108-->
        <form @submit.prevent="addIpAddress" v-if="shouldShowForm">
            <div class="flyform--group">
                <input id="ip_address" type="text" name="value" v-model="form.ip_address" placeholder=" ">
                <label for="ip_address">IP Address</label>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <span class="btn" v-if="privateIpAddresses.length" @click.prevent="resetForm">Cancel</span>
                    <button class="btn btn-primary" type="submit">Add Private IP Address</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    data () {
      return {
        showForm: false,
        form: this.createForm({
          ip_address: null
        })
      };
    },
    methods: {
      deleteIp(ip) {
        this.updateIpAddresses(this.privateIpAddresses.filter((privateIp) => {
          return privateIp !== ip;
        }));
      },
      addIpAddress() {
        let ipAddresses = Object.assign([], this.privateIpAddresses);
        ipAddresses.push(this.form.ip_address);
        this.updateIpAddresses(ipAddresses)
      },
      updateIpAddresses (ipAddresses) {
          this.$store
            .dispatch("user_servers/updatePrivateIps", {
              server : this.server.id,
              ip_addresses : ipAddresses
            })
            .then(() => {
                this.form.reset();
                this.showForm = false;
            });
      },
    },
    computed: {
      server() {
        return this.$store.state.user_servers.server;
      },
      shouldShowForm () {
        return (
          (this.privateIpAddresses.length === 0) || this.showForm
        );
      },
      privateIpAddresses() {
        return this.server.private_ips || []
      }
    }
}
</script>
