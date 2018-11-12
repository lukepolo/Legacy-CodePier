<template>
    <section>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">My Servers</h3>
            <div class="section-content">
                <div class="container">
                    <ul class="wizard">
                        <li class="wizard-item" v-bind:class="{ 'router-link-active': !showArchive }">
                            <a @click="showArchive=false">Active Servers</a>
                        </li>
                        <li class="wizard-item" v-bind:class="{ 'router-link-active': showArchive }">
                            <a @click="showArchive=true">Archived Servers</a>
                        </li>
                    </ul>


                    <table class="table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>IP</th>
                            <th>Status</th>
                            <th>
                                Date
                                <template v-if="!showArchive">
                                    Created
                                </template>
                                <template v-else>
                                    Archived
                                </template>
                            </th>
                            <th class="text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="server in servers">
                            <td>
                                <template v-if="server.status === 'Provisioned'">
                                    <router-link :to="{ name : 'server_info', params : { server_id : server.id } }">
                                        {{ server.name }}
                                    </router-link>
                                </template>
                                <template v-else>
                                    {{ server.name }}
                                </template>
                            </td>
                            <td>{{ server.ip }}</td>
                            <td>
                                <template v-if="server.deleted_at">
                                    Archived
                                </template>
                                <template v-else>
                                    <template v-if="server.status !==  'Provisioned' && server.custom_server_url">
                                        <div class="flex flex--center">
                                            <textarea rows="2" style="min-width:350px;" readonly>{{ server.custom_server_url }}</textarea>
                                            <div class="flex--spacing"><clipboard :data="server.custom_server_url"></clipboard></div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        {{ server.status }}
                                    </template>
                                    <!--<confirm dispatch="user_servers/archive" :params="server.id">-->
                                        <!--Archive Server-->
                                    <!--</confirm>-->
                                </template>
                            </td>
                            <td>
                                <template v-if="!showArchive">
                                    {{ parseDate(server.created_at).format('LLLL') }}
                                </template>
                                <template v-else>
                                    {{ parseDate(server.updated_at).format('LLLL') }}
                                </template>

                            </td>
                            <td class="text-right">
                                <template v-if="server.deleted_at">
                                    <confirm dispatch="user_servers/restore" :params="server.id">
                                        <span class="icon-refresh2"></span> &nbsp; Restore
                                    </confirm>
                                    <confirm confirm_position="left" dispatch="user_servers/deleteServer" confirm_class="btn btn-danger" message="This does not delete this from your server provider!" :params="server.id">
                                        <span class="icon-trash"></span> &nbsp; Delete
                                    </confirm>
                                </template>
                                <template v-else>
                                    <confirm dispatch="user_servers/archive" :params="server.id">
                                        <span class="icon-archive"></span> &nbsp; Archive
                                    </confirm>
                                </template>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="flyform--footer">

                        <h3>
                            While we do offer the ability to create a server manually, we suggest creating a site first,<br>
                            and let the site dictate how your server is built.
                        </h3>
                        <br><br>

                        <div class="flyform--footer-btns">
                            <confirm confirm_class="btn btn-primary">
                                <div slot="form">
                                    <p>By creating and customizing a site first CodePier will set up server defaults automatically.</p>
                                    <div class="alert alert-error">
                                        You should only do this if you have advanced server knowledge.
                                    </div>
                                </div>
                                <router-link slot="confirm-button" :to="{ name : 'server_form' }" class="btn btn-small btn-danger" :class="{ 'btn-disabled' : !serverCreateEnabled }">
                                    I Understand
                                </router-link>
                                Create Server
                            </confirm>

                        </div>
                    </div>

                </div>
            </div>
        </section>
    </section>
</template>

<script>
export default {
  data() {
    return {
      showArchive: false
    };
  },
  watch: {
    showArchive: function() {
      this.$store.dispatch("user_servers/getTrashed");
    }
  },
  computed: {
    servers() {
      if (!this.showArchive) {
        return this.$store.state.user_servers.servers;
      }
      return this.$store.state.user_servers.trashed;
    }
  }
};
</script>
