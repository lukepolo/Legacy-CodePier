<template>
  <section>
    <div class="flex flex--center">
      <h3 class="flex--grow">SSL Certificates</h3>
      <tooltip message="Add SSL Certificate">
        <span
          class="btn btn-small btn-primary"
          :class="{ 'btn-disabled': showForm }"
          @click="showForm = true"
        >
          <span class="icon-plus"></span>
        </span>
      </tooltip>
    </div>

    <br />
    <template v-if="sslCertificates.length">
      <div class="box" v-for="sslCertificate in sslCertificates">
        <div class="box--heading">
          <div>
            <div class="text-primary">
              <tooltip message="Wildcard" v-if="sslCertificate.wildcard">
                <span class="btn--link btn--link-primary">*</span>
              </tooltip>
              {{ sslCertificate.domains }}
              <span class="text-success" v-if="sslCertificate.active">
                <i class="icon-check_circle"></i>
              </span>
            </div>
            <div class="muted">{{ sslCertificate.type }}</div>
          </div>

          <div class="box--heading-btns">
            <template v-if="isRunningCommandFor(sslCertificate.id)">
              {{ isRunningCommandFor(sslCertificate.id).status }}
            </template>
            <template v-else>
              <template v-if="sslCertificate.failed">
                <template v-if="!sslCertificate.wildcard">
                  <tooltip message="Retry Install">
                    <span class="btn--link btn--link-warning">
                      <a
                        @click="
                          retryInstall(
                            sslCertificate.domains,
                            sslCertificate.type,
                          )
                        "
                      >
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
                    <a @click="deactivateSslCertificate(sslCertificate.id)"
                      ><span class="icon-cancel"></span
                    ></a>
                  </span>
                </tooltip>
                <tooltip message="Activate" v-else>
                  <span class="table--action-activate">
                    <a @click="activateSslCertificate(sslCertificate.id)"
                      ><span class="icon-check_circle"></span
                    ></a>
                  </span>
                </tooltip>
              </template>
              <tooltip message="Delete">
                <span class="table--action-delete">
                  <a @click="deleteSslCertificate(sslCertificate.id)"
                    ><span class="icon-trash"></span
                  ></a>
                </span>
              </tooltip>
            </template>
          </div>
        </div>
        <div class="box--content">
          <p><label>Key Path</label>{{ sslCertificate.key_path }}</p>
          <p><label>Cert Path</label>{{ sslCertificate.cert_path }}</p>
          <template v-if="sslCertificate.wildcard">
            <hr />
            <div class="wildcard--info">
              <div class="flex">
                <div class="flex--grow">
                  <tooltip message="Show Me How">
                    <a
                      target="_blank"
                      href="https://support.google.com/a/answer/47283"
                      ><span class="icon-info"></span
                    ></a>
                  </tooltip>
                  To install, please create a CNAME :
                </div>
                <div class="flex--spacing" style="margin-top: -5px;">
                  <span
                    class="btn btn-small btn-primary"
                    :class="{
                      'btn-disabled':
                        !siteServers ||
                        siteServers.length === 0 ||
                        isRunningCommandFor(sslCertificate.id),
                    }"
                    @click="tryWildcardInstall(sslCertificate.id)"
                  >
                    <template v-if="isRunningCommandFor(sslCertificate.id)">
                      Getting Cert
                    </template>
                    <template
                      v-else-if="!siteServers || siteServers.length === 0"
                    >
                      Connect a server first
                    </template>
                    <template v-else>
                      Get Cert
                    </template>
                  </span>
                </div>
              </div>
              <div class="flex flex--center">
                <label>for host</label>
                <div class="flex--grow flex--spacing">
                  <input
                    readonly
                    type="text"
                    :value="'_acme-challenge.' + sslCertificate.domains"
                  />
                </div>
                <tooltip message="Copy to Clipboard" placement="top">
                  <clipboard
                    :data="'_acme-challenge.' + sslCertificate.domains"
                  ></clipboard>
                </tooltip>
              </div>
              <div class="flex flex--center">
                <label>to destination</label>
                <div class="flex--grow flex--spacing">
                  <input
                    readonly
                    type="text"
                    :value="sslCertificate.acme_fulldomain"
                  />
                </div>

                <tooltip message="Copy to Clipboard" placement="top">
                  <clipboard :data="sslCertificate.acme_fulldomain"></clipboard>
                </tooltip>
              </div>
            </div>
          </template>
        </div>
      </div>
    </template>
    <template v-if="showForm">
      <div class="flyform--group"><label>Certificate Type</label></div>

      <div class="grid-2">
        <div v-if="!serverId">
          <base-radio
            label="Let's Encrypt"
            v-model="form.type"
            name="type"
            value="Let's Encrypt"
          ></base-radio>
        </div>
        <div>
          <base-radio
            label="Existing Certificate"
            v-model="form.type"
            name="type"
            value="existing"
          ></base-radio>
        </div>
      </div>

      <template v-if="form.type !== 'codepier_existing'">
        <template v-if="form.type">
          <div class="flyform--group">
            <div class="flyform--group-prefix">
              <input
                type="text"
                v-model="form.domains"
                name="domains"
                placeholder=" "
              />
              <label for="domains"
                >Domain<span v-if="!form.wildcard">(s)</span></label
              >
              <!--<template v-if="form.wildcard">-->
              <!--<div class="flyform&#45;&#45;group-prefix-label">wildcard</div>-->
              <!--</template>-->
            </div>
          </div>

          <div class="flyform--group-checkbox" v-if="!serverId">
            <label>
              <input type="checkbox" name="wildcard" v-model="form.wildcard" />
              <span class="icon"></span> Wildcard
            </label>
          </div>
        </template>

        <template v-if="form.type === 'existing'">
          <div class="flyform--group">
            <label>Private Key</label>
            <textarea
              name="private_key"
              v-model="form.private_key"
              required
            ></textarea>
          </div>

          <div class="flyform--group">
            <label>Certificate</label>
            <textarea
              name="certificate"
              v-model="form.certificate"
              required
            ></textarea>
          </div>
        </template>

        <form @submit.prevent="installCertificate">
          <div class="flyform--footer">
            <div class="flyform--footer-btns">
              <span
                class="btn"
                v-if="sslCertificates.length"
                @click.prevent="resetForm"
                >Cancel</span
              >
              <button class="btn btn-primary" type="submit">
                Add Certificate
              </button>
            </div>
          </div>
        </form>
      </template>
      <template v-else>
        <form @submit.prevent="activateSslCertificate(installExistingCertId)">
          <div class="flyform--group">
            <label>Select SSL Certificate</label>
            <div class="flyform--group-select">
              <select name="ssl_certificate_id" v-model="installExistingCertId">
                <option></option>
                <option
                  v-for="sslCertificate in availableSslCertificates"
                  :value="sslCertificate.id"
                  >{{ sslCertificate.wildcard ? "*." : ""
                  }}{{ sslCertificate.domains }}</option
                >
              </select>
            </div>
          </div>

          <div class="flyform--footer">
            <div class="flyform--footer-btns">
              <span
                class="btn"
                v-if="sslCertificates.length"
                @click.prevent="resetForm"
                >Cancel</span
              >
              <button
                class="btn btn-primary"
                :class="{ 'btn-disabled': !installExistingCertId }"
                type="submit"
              >
                Install Certificate
              </button>
            </div>
          </div>
        </form>
      </template>
    </template>
  </section>
</template>

<script>
export default {
  data() {
    return {
      showForm: false,
      form: this.createForm({}).validation({
        rules: {},
      }),
    };
  },
  watch: {
    $route: {
      immediate: true,
      handler: "fetchData",
    },
    sslCertificates: {
      handler() {
        console.info(this.sslCertificates);
        if (!this.sslCertificates.length) {
          this.showForm = true;
        }
      },
    },
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user/sites/sslCertificates/get", {
        site: this.$route.params.site,
      });
    },
    createSslCertificate() {
      this.$store
        .dispatch("user/sites/sslCertificates/create", {
          data: this.form.data(),
          parameters: {
            site: this.$route.params.site,
          },
        })
        .then(() => {
          this.cancel();
        });
    },
    deleteSslCertificate(sslCertificateId) {
      this.$store.dispatch("user/sites/sslCertificates/destroy", {
        ssh_key: sslCertificateId,
        site: this.$route.params.site,
      });
    },
    cancel() {
      this.form.reset();
      this.showForm = false;
    },
  },
  computed: {
    sslCertificates() {
      return this.$store.state.user.sites.sslCertificates.ssl_certificates;
    },
    serverId() {
      // TODO - should be in a mixin
      return;
    },
  },
};
</script>
