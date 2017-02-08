<template>
    <div v-if="site">
        <div class="jcf-form-wrap">
            <form @submit.prevent="installLetsEncryptCertificate">
                <div class="jcf-input-group">
                    <input type="text" v-model="domains" name="domains">
                    <label for="domains">
                        <span class="float-label">Domains</span>
                    </label>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Install Let's Encrypt Certificate</button>
                </div>
            </form>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Domains</th>
                    <th>Type</th>
                    <th>Cert Path</th>
                    <th>Key Path</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ssl_certificate in ssl_certificates">
                    <td>{{ ssl_certificate.domains }}</td>
                    <td>{{ ssl_certificate.type }}</td>
                    <td>{{ ssl_certificate.cert_path }}</td>
                    <td>{{ ssl_certificate.key_path }}</td>
                    <td>
                        <template v-if="isRunningCommandFor(ssl_certificate.id)">
                            {{ isRunningCommandFor(ssl_certificate.id).status }}
                        </template>
                        <template v-else>
                            <template v-if="ssl_certificate.failed">
                                <a @click="retryInstall(ssl_certificate.domains)">Retry Install</a>
                            </template>
                            <template v-else>
                                <a @click="deactivateSslCertificate(ssl_certificate.id)" v-if="ssl_certificate.active">Deactivate</a>
                                <a @click="activateSslCertificate(ssl_certificate.id)" v-else>Activate</a>
                                <a @click="deleteSslCertificate(ssl_certificate.id)" href="#">Delete</a>
                            </template>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                domains: null
            }
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getSslCertificates', this.$route.params.site_id);
            },
            installLetsEncryptCertificate() {
                this.$store.dispatch('installSslCertificate', {
                    site_id: this.site.id,
                    domains: this.domains,
                    type : 'Let\'s Encrypt'
                }).then(() => {
                    this.form = this.$options.data()
                })
            },
            activateSslCertificate : function(ssl_certificate_id) {
                this.$store.dispatch('updateSslCertificate', {
                    active : true,
                    site : this.site.id,
                    ssl_certificate : ssl_certificate_id,
                })
            },
            deactivateSslCertificate : function(ssl_certificate_id) {
                this.$store.dispatch('updateSslCertificate', {
                    active : false,
                    site : this.site.id,
                    ssl_certificate : ssl_certificate_id,
                })
            },
            deleteSslCertificate: function (ssl_certificate_id) {
                this.$store.dispatch('deleteSslCertificate', {
                    site : this.site.id,
                    ssl_certificate : ssl_certificate_id,
                })
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\SslCertificate', id);
            },
            retryInstall(domains) {
                this.$store.dispatch('installSslCertificate', {
                    site_id: this.site.id,
                    domains: domains,
                    type : 'Let\'s Encrypt'
                }).then(() => {
                    this.form = this.$options.data()
                })
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            ssl_certificates() {
                return this.$store.state.siteSslCertificatesStore.ssl_certificates;
            }
        }
    }
</script>