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
                        <confirm-dropdown dispatch="user_servers/refreshSudoPassword" :params="{ server : server.id }">
                            sudo Password
                            <tooltip message="Refresh Sudo Password">
                                <a href="#"><span class="fa fa-refresh"></span></a>
                            </tooltip>
                        </confirm-dropdown>
                    </h3>

                    <button class="btn btn-primary" @click.stop="revealSudoPassword">Reveal</button>

                    <div class="flyform--group flyform--group-nomargin">
                        <textarea rows="1" readonly>{{ sudoPassword }}</textarea>
                    </div>

                    <div class="text-right">
                        <tooltip message="Copy to Clipboard">
                            <clipboard :data="sudoPassword"></clipboard>
                        </tooltip>
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

                    <button class="btn btn-primary" @click.stop="revealDatabasePassword">Reveal</button>

                    <div class="flyform--group flyform--group-nomargin">
                        <textarea rows="1" readonly>{{ databasePassword }}</textarea>
                    </div>

                    <div class="text-right">
                        <tooltip message="Copy to Clipboard">
                            <clipboard :data="databasePassword"></clipboard>
                        </tooltip>
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
        sudoPassword : null,
        databasePassword : null,
    }
  },
  methods: {
    revealSudoPassword() {
      this.$store.dispatch('user_servers/getSudoPassword', {
        server : this.server.id
      }).then((response) => {
        this.sudoPassword = response;
      })
    },
    revealDatabasePassword() {
      this.$store.dispatch('user_servers/getDatabasePassword', {
        server : this.server.id
      }).then((response) => {
        this.databasePassword = response;
      })
    },
  },
  computed: {
    server() {
      return this.$store.state.user_servers.server;
    },
  }
};
</script>
