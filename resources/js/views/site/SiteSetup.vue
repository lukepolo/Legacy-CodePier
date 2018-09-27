<template>
    <div v-if="site">
        <base-form action="updateSite">
            <pre>{{ form.data(false) }}</pre>

            <repository-provider-selector v-model="form.user_repository_provider_id" :custom.sync="form.custom_provider"></repository-provider-selector>

            <h3 class="heading" v-if="!form.user_repository_provider_id && !form.custom_provider">
                To create your site, you need to select a source control provider.
            </h3>

            <template v-if="form.user_repository_provider_id || form.custom_provider">

                <div class="flyform--group">
                    <div class="flyform--group-prefix">
                        <input id="repository" ref="repository" type="text" v-model="form.repository" name="repository" placeholder=" " required>
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
                    <input id="branch" type="text" v-model="form.branch" name="branch" placeholder=" ">
                    <label for="branch">Branch</label>
                </div>

                <div class="flyform--group">
                    <div class="flyform--group-prefix">
                        <input id="web_directory" type="text" name="web_directory" v-model="form.web_directory" placeholder=" ">
                        <label for="web_directory" class="flyform--group-iconlabel">Web Directory</label>
                        <div class="flyform--group-prefix-label">
                            ~/codepier/{{site.domain}}/
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
                        <button class="btn btn-primary" type="submit" :disabled="form.isDirty()">Update Repository</button>
                    </div>
                </div>

            </template>
        </base-form>
    </div>
</template>

<script>
import RepositoryProviderSelector from "./components/setup-components/RepositoryProviderSelector";
export default {
  components: {
    RepositoryProviderSelector,
  },
  beforeCreate() {
    this.$store.dispatch("server/languages/get");
    this.$store.dispatch("server/frameworks/get");
    this.$store.dispatch("sourceControlProviders/get");
    this.$store.dispatch("user/sourceControlProviders/get");
  },
  data() {
    return {
      form: this.createForm({
        type: null,
        branch: "master",
        framework: null,
        repository: null,
        custom_provider: false,
        web_directory: "public",
        user_repository_provider_id: null,
      }),
    };
  },
  watch: {
    site: {
      immediate: true,
      handler() {
        this.form.merge(this.site);
      },
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
            this.form.fill({
              user_repository_provider_id: provider.id,
            });
          }
        }
      }
    },
  },
  methods: {
    getProviderByUrl(providerUrl) {
      let provider = this.repositoryProviders.find((repositoryProvider) => {
        return repositoryProvider.url === providerUrl;
      });
      if (provider) {
        return this.userRepositoryProviders.find((repositoryProvider) => {
          return provider.id === repositoryProvider.repository_provider_id;
        });
      }
    },
  },
  computed: {
    availableLanguages() {
      return this.$store.state.server.languages.languages;
    },
    availableFrameworks() {
      return this.$store.state.server.frameworks.frameworks;
    },
    repositoryProviders() {
      return this.$store.state.sourceControlProviders.providers;
    },
    userRepositoryProviders() {
      return this.$store.state.user.sourceControlProviders.providers;
    },
    site() {
      return this.$store.getters["user/sites/show"](this.$route.params.site);
    },
    repositoryUrl() {
      return `${this.providerUrl}/${this.form.repository}`;
    },
    providerUrl() {
      let userRepository = this.userRepositoryProviders.find(
        (repositoryProvider) => {
          return (
            repositoryProvider.id === this.form.user_repository_provider_id
          );
        },
      );
      if (userRepository) {
        let provider = this.repositoryProviders.find((repositoryProvider) => {
          return (
            repositoryProvider.id === userRepository.repository_provider_id
          );
        });
        return provider && provider.url;
      }
    },
  },
};
</script>
