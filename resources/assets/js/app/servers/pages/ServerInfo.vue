<template>
    <section v-if="server">
        <div class="providers grid-2">
            <drop-down tag="span">
                <div class="grid--item" slot="header">
                    <div class="providers--item">
                        <div class="providers--item-header">
                            <div class="providers--item-icon">
                                <span class="fa fa-key"></span>
                            </div>
                        </div>
                        <div class="providers--item-footer">
                            <div class="providers--item-footer-connect">
                                <h4 class="providers--title">Sudo Password</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div slot="content" class="dropdown-menu dropdown-content nowrap">

                    <h3>
                        <confirm-dropdown :params="{ server : server.id }">
                            Sudo Password
                            <tooltip message="Refresh Sudo Password" v-if="server.progress >= 100">
                                <a @click.prevent.stop="refreshSudoPassword" href="#"><span class="fa fa-refresh"></span></a>
                            </tooltip>
                        </confirm-dropdown>
                    </h3>

                    <button class="btn btn-primary btn-full" @click.stop="revealSudoPassword" v-if="!sudoPassword"><span class="icon-visibility"></span> Reveal</button>

                    <div class="flex flex--center" v-if="sudoPassword">
                        <div class="flex--grow">
                            <div class="flyform--group flyform--group-nomargin">
                                <textarea rows="1" readonly>{{ sudoPassword }}</textarea>
                            </div>

                            <div class="text-right">
                                <tooltip message="Copy to Clipboard">
                                    <clipboard :data="sudoPassword"></clipboard>
                                </tooltip>
                            </div>
                        </div>
                    </div>
                </div>
            </drop-down>

            <drop-down tag="span">
                <div class="grid--item" slot="header">
                    <div class="providers--item">
                        <div class="providers--item-header">
                            <div class="providers--item-icon">
                                <span class="icon-database"></span>
                            </div>
                        </div>
                        <div class="providers--item-footer">
                            <div class="providers--item-footer-connect">
                                <h4 class="providers--title">Database Password</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div slot="content" class="dropdown-menu dropdown-content nowrap">

                    <h3>
                        Database Password
                    </h3>

                    <button class="btn btn-primary btn-full" v-if="!databasePassword" @click.stop="revealDatabasePassword"><span class="icon-visibility"></span> Reveal</button>

                    <div class="flex flex--center" v-if="databasePassword">
                        <div class="flex--grow">
                            <div class="flyform--group flyform--group-nomargin">
                                <textarea rows="1" readonly>{{ databasePassword }}</textarea>
                            </div>

                            <div class="text-right">
                                <tooltip message="Copy to Clipboard">
                                    <clipboard :data="databasePassword"></clipboard>
                                </tooltip>
                            </div>
                        </div>
                    </div>

                </div>
            </drop-down>


            <drop-down tag="span">
                <div class="grid--item" slot="header">
                    <div class="providers--item">
                        <div class="providers--item-header">
                            <div class="providers--item-icon">
                                <span class="icon-web"></span>
                            </div>
                        </div>
                        <div class="providers--item-footer">
                            <div class="providers--item-footer-connect">
                                <h4 class="providers--title">
                                    Public SSH KEY
                                    <small>Used to connect to your server from CodePier</small>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div slot="content" class="dropdown-menu dropdown-content nowrap">

                    <h3>
                        Public SSH KEY
                    </h3>

                    <div class="flex flex--center">
                        <div class="flex--grow">
                            <div class="flyform--group flyform--group-nomargin">
                                <textarea rows="5" readonly>{{ server.ssh_key }}</textarea>
                            </div>

                            <div class="text-right">
                                <tooltip message="Copy to Clipboard">
                                    <clipboard :data="server.ssh_key"></clipboard>
                                </tooltip>
                            </div>
                        </div>
                    </div>

                </div>
            </drop-down>
        </div>

    </section>
</template>

<script>
export default {
  data() {
    return {
      sudoPassword: null,
      databasePassword: null
    };
  },
  methods: {
    revealSudoPassword() {
      this.$store
        .dispatch("user_servers/getSudoPassword", {
          server: this.server.id
        })
        .then(response => {
          this.sudoPassword = response;
        });
    },
    refreshSudoPassword() {
      this.$store
        .dispatch("user_servers/refreshSudoPassword", {
          server: this.server.id
        })
        .then(response => {
          this.sudoPassword = response;
        });
    },
    revealDatabasePassword() {
      this.$store
        .dispatch("user_servers/getDatabasePassword", {
          server: this.server.id
        })
        .then(response => {
          this.databasePassword = response;
        });
    }
  },
  computed: {
    server() {
      return this.$store.state.user_servers.server;
    }
  }
};
</script>
