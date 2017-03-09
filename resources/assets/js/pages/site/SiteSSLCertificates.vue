<template>
    <div v-if="site">
        <div class="jcf-form-wrap">
            <form @submit.prevent="installCertificate">

                <div class="jcf-input-group input-radio">
                    <div class="input-question">Certificate Type</div>
                    <label>
                        <input name="type" type="radio" v-model="form.type" value="Let's Encrypt">
                        <span class="icon"></span>
                        Let's Encrypt
                    </label>
                    <label>
                        <input name="type" type="radio" v-model="form.type" value="existing">
                        <span class="icon"></span>
                        Existing Certificate
                    </label>
                </div>

                <template v-if="form.type">
                    <div class="jcf-input-group">
                        <input type="text" v-model="form.domains" name="domains">
                        <label for="domains">
                            <span class="float-label">Domains</span>
                        </label>
                    </div>
                </template>

                <template v-if="form.type == 'existing'">
                    <div class="jcf-input-group">
                        <div class="input-question">Private Key</div>
                        <textarea name="private_key" v-model="form.private_key"></textarea>
                    </div>

                    <div class="jcf-input-group">
                        <div class="input-question">Certificate</div>
                        <textarea name="certificate" v-model="form.certificate"></textarea>
                    </div>
                </template>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Add Certificate</button>
                </div>

            </form>

        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Domains</th>
                    <th>Type</th>
                    <!--<th>Cert Path</th>-->
                    <!--<th>Key Path</th>-->
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ssl_certificate in ssl_certificates" :key="ssl_certificate">
                    <td>{{ ssl_certificate.domains }}</td>
                    <td>{{ ssl_certificate.type }}</td>
                    <!--<td>{{ ssl_certificate.cert_path }}</td>-->
                    <!--<td>{{ ssl_certificate.key_path }}</td>-->
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
                            </template>
                            <a @click="deleteSslCertificate(ssl_certificate.id)">Delete</a>
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
                form  : {
                    type : null,
                    domains : null,
                    private_key : null,
                    certificate : null,
                }
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
            installCertificate() {
                this.$store.dispatch('installSslCertificate', {
                    site_id: this.site.id,
                    type : this.form.type,
                    domains: this.form.domains,
                    private_key : this.form.private_key,
                    certificate : this.form.certificate,
                }).then((data) => {
                    if(data) {
                        this.$data.form = this.$options.data().form
                    }
                })
            },
            activateSslCertificate : function(ssl_certificate_id) {
                this.$store.dispatch('updateSslCertificate', {
                    active : true,
                    site : this.site.id,
                    ssl_certificate : ssl_certificate_id
                })
            },
            deactivateSslCertificate : function(ssl_certificate_id) {
                this.$store.dispatch('updateSslCertificate', {
                    active : false,
                    site : this.site.id,
                    ssl_certificate : ssl_certificate_id
                })
            },
            deleteSslCertificate: function (ssl_certificate_id) {
                this.$store.dispatch('deleteSslCertificate', {
                    site : this.site.id,
                    ssl_certificate : ssl_certificate_id
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
                    this.$data.form = this.$options.data().form
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