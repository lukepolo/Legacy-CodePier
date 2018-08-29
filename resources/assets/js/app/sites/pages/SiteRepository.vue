<template>
    <div>
        <p class="info">
            Piles are groupings for your sites. We've built defaults for you, but you can edit them to fit your needs. To get started create a site.
        </p>

        <form @submit.prevent="updateOrCreate">


            <div class="flyform--group">
                <label>Select Pile</label>
                <div class="flyform--group-select">
                    <select name="pile" v-model="form.pile_id">
                        <option v-for="pile in piles" :value="pile.id">{{ pile.name }}</option>
                    </select>
                </div>
            </div>

            <div class="flyform--group">
                <input ref="domain" name="domain" v-model="form.domain" type="text" placeholder=" ">
                <label for="domain">Domain / Alias</label>
            </div>
            <div class="flyform--group-checkbox">
                <label>
                    <input type="checkbox" v-model="form.wildcard_domain" name="wildcard_domain" value="1">
                    <span class="icon"></span>
                    Wildcard Domain
                    <tooltip :message="'If your site requires wildcard for sub domains'" size="medium">
                        <span class="fa fa-info-circle"></span>
                    </tooltip>
                </label>
            </div>

            <div :class="{ 'section--disabled' : !form.domain || form.domain.length === 0 }">
                <repository-provider-selector :provider.sync="form.user_repository_provider_id">
                    <input type="radio" checked v-if="form.custom_provider">

                    <div class="providers--item providers--item-custom" @click="form.custom_provider = true">
                        <div class="providers--item-header">
                            <div class="providers--item-name"><h3>Custom</h3></div>
                        </div>
                    </div>
                </repository-provider-selector>

            </div>


            <div :class="{ 'section--disabled' : !form.user_repository_provider_id && !form.custom_provider }">

                <div class="flyform--group">
                    <div class="flyform--group-prefix">
                        <input ref="repository" type="text" v-model="form.repository" name="repository" placeholder=" " required>
                        <label for="repository">Repository URL</label>
                        <template v-if="!form.custom_provider">
                            <div class="flyform--group-prefix-label">
                                {{ providerUrl }}/
                            </div>
                        </template>
                    </div>

                    <template v-if="!form.custom_provider">
                        <div class="flyform--input-icon-right">
                            <a target="_blank" :href="'//'+repositoryUrl">
                                <tooltip message="Check URL" class="tooltip-in-form">
                                    <span class="icon-link"></span>
                                </tooltip>
                            </a>
                        </div>
                    </template>
                </div>

                <div class="flyform--group">
                    <input type="text" v-model="form.branch" name="branch" placeholder=" ">
                    <label for="branch">Branch</label>
                </div>

                <div class="flyform--group">
                    <div class="flyform--group-prefix">
                        <input type="text" name="web_directory" v-model="form.web_directory" placeholder=" ">
                        <label for="web_directory" class="flyform--group-iconlabel">Web Directory</label>
                        <div class="flyform--group-prefix-label">
                            ~/codepier/{{ form.domain }}/
                        </div>
                        <tooltip message="The location of your apps entry" size="medium">
                            <span class="fa fa-info-circle"></span>
                        </tooltip>

                    </div>
                </div>

                <div class="flyform--group">
                    <label>Language & Framework</label>
                    <div class="flyform--group-select" v-if="Object.keys(availableLanguages).length && Object.keys(availableFrameworks).length">
                        <select v-model="form.type" name="type" required>
                            <option value=""></option>
                            <template v-for="(features, language) in availableLanguages">
                                <optgroup :label="language">
                                    <option :value="language" v-if="language !== 'Swift'">
                                        {{ language | startCase }}
                                    </option>
                                    <option v-for="(features, framework) in availableFrameworks[language]" :value="language+'.'+framework"> {{ framework | startCase }}</option>
                                </optgroup>
                            </template>
                        </select>

                    </div>
                </div>

                <div class="flyform--footer">
                    <div class="flyform--footer-btns">
                        <button class="btn btn-primary" type="submit" :disabled="form.diff().length === 0">
                            <span v-if="site">Update Repository</span>
                            <span v-else>Create Site</span>
                        </button>
                    </div>
                </div>

            </div>

        </form>


    </div>
</template>

<script>
import RepositoryProviderSelector from "./../components/RepositoryProviderSelector";

export default {
  components: {
    RepositoryProviderSelector
  },
  data() {
    return {
      form: this.createForm({
        type: null,
        domain : null,
        pile_id : null,
        framework: null,
        repository: null,
        branch: "master",
        custom_provider: false,
        web_directory: "public",
        user_repository_provider_id: null
      }),
    };
  },
  created() {
    this.fetchData();
    this.siteChange();
  },
  watch: {
    $route: "fetchData",
    site: "siteChange",
    "$data.form.user_repository_provider_id": function(value) {
      if (value) {
        this.form.custom_provider = false;
      }
    },
    "$data.form.custom_provider": function(value) {
      if (value) {
        Vue.set(this.form, "user_repository_provider_id", null);
      }
    },
    "form.repository": function(value) {
      if (value.indexOf("http") > -1) {
        let parser = document.createElement("a");
        parser.href = value;

        let path = parser.pathname.substring(1);

        if (this.form.repository !== path) {
          this.form.repository = path;

          let provider = this.getProviderByUrl(parser.hostname);

          if (provider) {
            this.form.user_repository_provider_id = provider.id;
          }
        }
      }
    }
  },
  methods: {
    fetchData() {
      this.$store.dispatch("server_languages/get"); // TODO -cache it
      this.$store.dispatch("server_frameworks/get"); // TODO -cache it
      this.$store.dispatch("repository_providers/get"); // TODO -cache it
      this.$store.dispatch(
        "user_repository_providers/get",
        this.$store.state.user.user.id
      );
    },
    siteChange() {
      this.form.empty();

      let site = this.site;

      if (site && site.repository) {
        this.form.domain = site.domain;
        this.form.type = site.framework ? site.framework : site.type;
        this.form.branch = site.branch;
        this.form.repository = site.repository;
        this.form.web_directory = site.web_directory;
        this.form.user_repository_provider_id = site.user_repository_provider_id;

        if (this.form.repository && !this.form.user_repository_provider_id) {
          this.form.custom_provider = true;
        }
      }

      this.form.setOriginalData();
    },
    updateOrCreate() {
        if(this.site) {
            return this.updateSite();
        }
        this.createSite();
    },
    createSite() {
        this.$store.dispatch('user_sites/store', this.form);
    },
    updateSite() {
      let tempType = _.split(this.form.type, ".");
      let type = tempType[0];
      let framework = null;
      if (tempType.length === 2) {
        framework = tempType[0] + "." + tempType[1];
      }

      this.$store.dispatch("user_sites/update", {
        type,
        framework,
        site: this.site.id,
        branch: this.form.branch,
        domain: this.site.domain,
        pile_id: this.site.pile_id,
        repository: this.form.repository,
        web_directory: this.form.web_directory,
        custom_provider: this.form.custom_provider,
        user_repository_provider_id: !this.form.custom_provider ? this.form.user_repository_provider_id : null
      });
    },
    getProviderByUrl(providerUrl) {
      let provider = _.find(this.repository_providers, repositoryProvider => {
        return repositoryProvider.url === providerUrl;
      });

      if (provider) {
        return _.find(this.repository_providers, {
          id: provider.id
        });
      }
    }
  },
  computed: {
    site() {
      return this.$store.state.user_sites.site;
    },
    piles() {
        return this.$store.state.user_piles.piles;
    },
    repository_providers() {
      return this.$store.state.repository_providers.providers;
    },
    user_repository_providers() {
      return this.$store.state.user_repository_providers.providers;
    },
    availableLanguages() {
      return this.$store.state.server_languages.languages;
    },
    availableFrameworks() {
      return this.$store.state.server_frameworks.frameworks;
    },
    providerUrl() {
      if (this.form.user_repository_provider_id) {
        let userRepository = _.find(this.user_repository_providers, {
          id: this.form.user_repository_provider_id
        });

        if (userRepository) {
          let repositoryProvider = _.find(this.repository_providers, {
            id: userRepository.repository_provider_id
          });

          if (repositoryProvider) {
            return repositoryProvider.url;
          }
        }
      }
    },
    repositoryUrl() {
      return (
        this.providerUrl +
        "/" +
        (this.form.repository ? this.form.repository : "")
      );
    }
  }
};
</script>
