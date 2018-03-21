<template>
    <section>

        <div class="flex flex--center">
            <h3 class="flex--grow">
                SSL Certificates
            </h3>

            <tooltip message="Add SSL Certificate">
                <span class="btn btn-small btn-primary" :class="{ 'btn-disabled' : this.shouldShowForm }" @click="showForm = true">
                    <span class="icon-plus"></span>
                </span>
            </tooltip>
        </div>

        <br>
        <template v-if="sslCertificates.length" v-for="sslCertificate in sslCertificates">
            <div class="box">
                <div class="box--heading">
                    <div>
                        <div class="text-primary">
                          <tooltip message="Wildcard" v-if="sslCertificate.wildcard">
                            <span class="btn--link btn--link-primary">*</span>
                          </tooltip>
                        {{ sslCertificate.domains }} </div>
                        <div class="muted">{{ sslCertificate.type }}</div>
                    </div>

                    <div class="box--heading-btns">
                        <template v-if="isRunningCommandFor(sslCertificate.id)">
                            {{ isRunningCommandFor(sslCertificate.id).status }}

                            <tooltip message="Delete">
                                <a @click="deleteSslCertificate(sslCertificate.id)">
                                  <span class="btn--link btn--link-danger">
                                    <span class="icon-trash"></span>
                                  </span>
                                </a>
                            </tooltip>
                        </template>
                        <template v-else>
                            <template v-if="sslCertificate.failed">
                                <template v-if="sslCertificate.wildcard">
                                    <tooltip message="Try Installing Wildcard">
                                        <a @click="tryWildcardInstall(sslCertificate.id)">
                                          <span class="btn--link btn--link-warning">
                                            <span class="icon-refresh2"></span>
                                          </span>
                                        </a>
                                    </tooltip>
                                </template>
                                <template v-else>
                                    <tooltip message="Retry Install">
                                        <span class="btn--link btn--link-warning">
                                            <a @click="retryInstall(sslCertificate.domains)">
                                              <span class="btn--link btn--link-warning">
                                                <span class="icon-refresh2"></span>
                                              </span>
                                            </a>
                                        </span>
                                    </tooltip>
                                </template>
                            </template>
                            <template v-else>
                                <tooltip message="Deactivate" v-if="sslCertificate.active">
                                    <span class="table--action-deactivate">
                                        <a @click="deactivateSslCertificate(sslCertificate.id)"><span class="icon-cancel"></span></a>
                                    </span>
                                </tooltip>
                                <tooltip message="Activate" v-else>
                                    <span class="table--action-activate">
                                        <a @click="activateSslCertificate(sslCertificate.id)"><span class="icon-check_circle"></span></a>
                                    </span>
                                </tooltip>
                            </template>
                            <tooltip message="Delete">
                                <span class="table--action-delete">
                                    <a @click="deleteSslCertificate(sslCertificate.id)"><span class="icon-trash"></span></a>
                                </span>
                            </tooltip>
                        </template>
                    </div>
                </div>
                <div class="box--content">
                  <p>
                      <label>Cert Path</label>{{ sslCertificate.cert_path }}
                  </p>
                  <p>
                      <label>Key Path</label>{{ sslCertificate.key_path }}
                  </p>
                  <template v-if="sslCertificate.wildcard">
                    <hr>
                    <div class="wildcard--info">
                      <div class="flex">
                        <div class="flex--grow">
                          <tooltip message="Show Me How">
                            <a target="_blank" href="https://support.google.com/a/answer/47283"><span class="icon-info"></span></a>
                          </tooltip>
                          To install, please make CNAME:
                        </div>
                        <div class="flex--spacing" style="margin-top: -5px;">
                          <span class="btn btn-small btn-primary">Get Cert</span>
                        </div>
                      </div>


                      <div class="flex flex--center">
                        <label>for host</label>
                        <div class="flex--grow flex--spacing">
                          <input type="text" :value="'_acme-challenge.' + sslCertificate.domains">
                        </div>
                      </div>
                      <div class="flex flex--center">
                        <label>to destination</label>
                        <div class="flex--grow flex--spacing">

                          <input type="text" :value="sslCertificate.acme_fulldomain">
                        </div>
                      </div>
                    </div>
                  </template>
                </div>
            </div>
        </template>

        <form @submit.prevent="installCertificate" v-if="shouldShowForm">

            <div class="flyform--group">
                <label>Certificate Type</label>
            </div>

            <div class="grid-3">
                <div v-if="!serverId" class="flyform--group-radio">
                    <label>
                        <input name="type" type="radio" v-model="form.type" value="Let's Encrypt">
                        <span class="icon"></span>
                        Let's Encrypt
                    </label>
                </div>
                <div class="flyform--group-radio">
                    <label>
                        <!-- TODO: update to Wildcard stuff -->
                        <input name="type" type="radio">
                        <span class="icon"></span>
                        Existing CodePier Certificate
                    </label>
                </div>
                <div class="flyform--group-radio">
                    <label>
                        <input name="type" type="radio" v-model="form.type" value="existing">
                        <span class="icon"></span>
                        Other Existing Certificate
                    </label>
                </div>
            </div>

            <template v-if="form.type">
                <div class="flyform--group">
                    <input type="text" v-model="form.domains" name="domains" placeholder=" ">
                    <label for="domains">Domain(s)</label>
                </div>
                <div class="flyform--group-checkbox">
                    <label>
                        <input type="checkbox" name="wildcard" v-model="form.wildcard">
                        <span class="icon"></span>
                        Wildcard
                    </label>
                </div>
            </template>

            <template v-if="form.type === 'existing'">
                <div class="flyform--group">
                    <label>Private Key</label>
                    <textarea name="private_key" v-model="form.private_key"></textarea>
                </div>

                <div class="flyform--group">
                    <label>Certificate</label>
                    <textarea name="certificate" v-model="form.certificate"></textarea>
                </div>
            </template>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <span class="btn" v-if="sslCertificates.length" @click.prevent="resetForm">Cancel</span>
                    <button class="btn btn-primary" type="submit">Add Certificate</button>
                </div>
            </div>


        </form>

        <input type="hidden" v-if="site">
    </section>
</template>

<script>
export default {
  data() {
    return {
      loaded: false,
      showForm: false,
      form: this.createForm({
        wildcard : false,
        type: null,
        domains: null,
        private_key: null,
        certificate: null
      })
    };
  },
  created() {
    this.fetchData();
  },
  watch: {
    $route: "fetchData"
  },
  methods: {
    fetchData() {
      if (this.siteId) {
        this.$store
          .dispatch("user_site_ssl_certificates/get", this.siteId)
          .then(() => {
            this.loaded = true;
          });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_ssl_certificates/get", this.serverId)
          .then(() => {
            this.loaded = true;
          });
      }
    },
    installCertificate() {
      if (this.siteId) {
        this.$store
          .dispatch("user_site_ssl_certificates/store", {
            site_id: this.siteId,
            type: this.form.type,
            domains: this.form.domains,
            wildcard : this.form.wildcard,
            private_key: this.form.private_key,
            certificate: this.form.certificate
          })
          .then(data => {
            if (data) {
              this.resetForm();
            }
          });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_ssl_certificates/store", {
            type: this.form.type,
            server_id: this.serverId,
            domains: this.form.domains,
            private_key: this.form.private_key,
            certificate: this.form.certificate
          })
          .then(data => {
            if (data) {
              this.resetForm();
            }
          });
      }
    },
    activateSslCertificate: function(ssl_certificate_id) {
      if (this.siteId) {
        this.$store.dispatch("user_site_ssl_certificates/update", {
          active: true,
          site: this.siteId,
          ssl_certificate: ssl_certificate_id
        });
      }
    },
    deactivateSslCertificate: function(ssl_certificate_id) {
      if (this.siteId) {
        this.$store.dispatch("user_site_ssl_certificates/update", {
          active: false,
          site: this.siteId,
          ssl_certificate: ssl_certificate_id
        });
      }
    },
    deleteSslCertificate: function(ssl_certificate_id) {
      if (this.siteId) {
        this.$store.dispatch("user_site_ssl_certificates/destroy", {
          site: this.siteId,
          ssl_certificate: ssl_certificate_id
        });
      }

      if (this.serverId) {
        this.$store.dispatch("user_server_ssl_certificates/destroy", {
          server: this.serverId,
          ssl_certificate: ssl_certificate_id
        });
      }
    },
    isRunningCommandFor(id) {
      return this.isCommandRunning("App\\Models\\SslCertificate", id);
    },
    tryWildcardInstall(id) {
      this.$store.dispatch("user_site_ssl_certificates/wildcardInstall", {
        site : this.siteId,
        ssl_certificate : id
      });
    },
    retryInstall(domains) {
      if (this.siteId) {
        this.$store
          .dispatch("user_site_ssl_certificates/store", {
            site_id: this.siteId,
            domains: domains,
            type: "Let's Encrypt"
          })
          .then(() => {
            this.form.reset();
          });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_ssl_certificates/store", {
            domains: domains,
            type: "Let's Encrypt",
            server_id: this.serverId
          })
          .then(() => {
            this.form.reset();
          });
      }
    },
    resetForm() {
      this.form.reset();
      if (this.siteId) {
        this.form.type = "Let's Encrypt";
        this.form.domains = this.site.domain;
      } else {
        this.form.type = "existing";
      }
      this.showForm = false;
    }
  },
  computed: {
    site() {
      let site = this.$store.state.user_sites.site;
      if (site) {
        this.form.domains = site.domain;
      }
      return site;
    },
    siteId() {
      let siteId = this.$route.params.site_id;
      if (siteId) {
        this.form.type = "Let's Encrypt";
      }
      return siteId;
    },
    serverId() {
      let serverId = this.$route.params.server_id;
      if (serverId) {
        this.form.type = "existing";
      }
      return serverId;
    },
    sslCertificates() {
      return this.$store.state.user_site_ssl_certificates.ssl_certificates;
    },
    shouldShowForm() {
      return (
        (this.loaded && this.sslCertificates.length === 0) || this.showForm
      );
    }
  }
};
</script>
