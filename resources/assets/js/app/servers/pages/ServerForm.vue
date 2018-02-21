<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Create New Server</h3>

            <div class="section-content">
                <div class="container">
                    <form @submit.prevent="createServer()" class="validation-form floating-labels">

                        <template v-if="siteId">
                            <input type="hidden" name="site" :value="siteId">
                        </template>

                        <template v-if="$route.params.type">
                            <input type="hidden" name="type" :value="$route.params.type">
                        </template>

                        <server-provider-selector :server_provider_id.sync="server_provider_id" :is_custom.sync="is_custom"></server-provider-selector>

                        <template v-if="is_custom || server_provider_id">
                            <div class="grid-2">
                                <div class="flyform--group">
                                    <input type="text" id="server_name" name="server_name" placeholder=" " required>
                                    <label for="server_name">Server Name</label>
                                </div>
                            </div>

                            <div class="grid-2">
                                <div class="flyform--group" v-if="is_custom">

                                    <input type="number" name="port" required value="22" placeholder=" ">
                                    <label for="port" class="flyform--group-iconlabel">Number of Releases to keep</label>

                                    <tooltip message="We will use this port ssh connections" size="medium">
                                        <span class="fa fa-info-circle"></span>
                                    </tooltip>
                                </div>
                            </div>

                            <template v-if="server_provider_id && server_options.length && server_regions.length">

                                <div class="flyform--group">
                                    <label>Server Size</label>
                                    <div class="flyform--group-select">
                                        <select name="server_option" v-model="form.serverOptionId">
                                            <option></option>
                                            <option v-for="option in server_options" :value="option.id">
                                                {{ option.description ? option.description + ' : ' : null }}
                                                {{ option.memory | ram }} RAM
                                                - {{ option.cpus }} vCPUs
                                                - {{ option.space | diskSize }} Disk
                                                - ${{ option.priceHourly }} / Hour
                                                - ${{ option.priceMonthly }} / Month
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flyform--group">
                                    <label>Region</label>
                                    <div class="flyform--group-select">
                                        <select name="server_region" v-model="form.serverOptionRegion">
                                            <option
                                                v-for="region in server_regions"
                                                :value="region.id"
                                                :disabled="!isServerOptionInRegion(region)"
                                            >{{ region.name }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="flyform--group">
                                    <label>Server Features</label>
                                </div>

                                <template v-for="feature in server_provider_features">
                                    <div class="flyform--group-checkbox">
                                        <label>
                                            <input
                                                type="checkbox"
                                                name="server_provider_features[]"
                                                :value="feature.id"
                                                :checked="feature.default"
                                            >
                                            <span class="icon"></span>{{ 'Enable ' + feature.feature }}
                                            <small>{{ feature.cost }}</small>
                                        </label>
                                    </div>
                                </template>
                            </template>


                            <div class="flyform--footer">
                                <div class="flyform--footer-links">
                                    <h3 v-if="$route.params.site_id">
                                        <tooltip message="We have configured your server based on your site language and framework." size="large">
                                            <span class="fa fa-info-circle"></span>
                                        </tooltip>
                                        Your server has been customized for your site<br>
                                        <small>
                                            <a @click="customize_server = !customize_server">Customize Server Settings (Advanced Users)</a>
                                        </small>
                                    </h3>
                                    <h3 v-else>
                                        Set up your server :
                                    </h3>
                                </div>
                            </div>

                            <server-features :update="false" v-show="customize_server"></server-features>

                            <div class="flyform--footer">
                                <div class="flyform--footer-btns">
                                    <button type="submit" class="btn btn-primary">Create Server</button>
                                </div>
                            </div>

                        </template>
                    </form>
                </div>
            </div>
        </section>
    </section>
</template>

<script>
import { ServerFeatures } from "../../setup/pages";
import LeftNav from "../../../components/LeftNav";
import ServerProviderSelector from "./../components/ServerProviderSelector";
export default {
  components: {
    LeftNav,
    ServerFeatures,
    ServerProviderSelector
  },
  data() {
    return {
      form: {
        serverOptionId: null,
        serverOptionRegion: null
      },
      is_custom: false,
      server_provider_id: null,
      customize_server: !this.$route.params.site_id
    };
  },
  watch: {
    server_provider_id: function() {
      if (this.server_provider_id) {
        this.getProviderData(this.server_provider_id);
      }
    },
    "form.serverOptionId": function() {
      let region = _.find(this.server_regions, {
        id: this.form.serverOptionRegion
      });
      if (region && !this.isServerOptionInRegion(region)) {
        Vue.set(this.form, "serverOptionRegion", null);
      }
    }
  },
  methods: {
    getProviderData(server_provider_id) {
      this.is_custom = false;
      let provider = _.find(this.server_providers, { id: server_provider_id })
        .provider_name;
      if (provider) {
        this.$store.dispatch("server_providers/getFeatures", provider);
        this.$store.dispatch("server_providers/getOptions", provider);
        this.$store.dispatch("server_providers/getRegions", provider);
      }
    },
    createServer() {
      this.$store
        .dispatch("user_servers/store", this.getFormData(this.$el))
        .then(server => {
          if (server.id) {
            if (this.siteId) {
              app.$router.push({
                name: "site_overview",
                params: { site_id: this.siteId }
              });
            } else {
              app.$router.push("/");
            }
          }
        });
    },
    isServerOptionInRegion(region) {
      let serverOption = _.find(this.server_options, {
        id: this.form.serverOptionId
      });
      if (serverOption && serverOption.meta && serverOption.meta.regions) {
        return _.indexOf(serverOption.meta.regions, region.provider_name) > -1;
      }
      return true;
    }
  },
  computed: {
    pile() {
      return this.$store.state.user.user.current_pile_id;
    },
    siteId() {
      return this.$route.params.site_id;
    },
    server_options() {
      return this.$store.state.server_providers.options;
    },
    server_regions() {
      return _.sortBy(this.$store.state.server_providers.regions, "name");
    },
    server_providers() {
      return this.$store.state.server_providers.providers;
    },
    server_provider_features() {
      return this.$store.state.server_providers.features;
    }
  }
};
</script>
