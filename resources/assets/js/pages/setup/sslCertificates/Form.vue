<template>
    <section>
        <div class="jcf-form-wrap">
            <form @submit.prevent="installCertificate">

                <div class="jcf-input-group input-radio">
                    <div class="input-question">Certificate Type</div>
                    <label v-if="!serverId">
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
                    <th>Cert Path</th>
                    <th>Key Path</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="ssl_certificate in ssl_certificates" :key="ssl_certificate">
                    <td>{{ ssl_certificate.domains }}</td>
                    <td>{{ ssl_certificate.type }}</td>
                    <td class="break-word">{{ ssl_certificate.cert_path }}</td>
                    <td class="break-word">{{ ssl_certificate.key_path }}</td>
                    <td class="table--action">
                        <template v-if="isRunningCommandFor(ssl_certificate.id)">
                            {{ isRunningCommandFor(ssl_certificate.id).status }}
                        </template>
                        <template v-else>
                            <template v-if="ssl_certificate.failed">
                                <tooltip message="Retry Install">
                                    <span class="table--action-retry">
                                        <a @click="retryInstall(ssl_certificate.domains)"><span class="icon-refresh"></span></a>
                                    </span>
                                </tooltip>
                            </template>
                            <template v-else-if="!server">
                                <tooltip message="Deactivate" v-if="ssl_certificate.active">
                                    <span class="table--action-deactivate">
                                        <a @click="deactivateSslCertificate(ssl_certificate.id)"><span class="icon-cancel"></span></a>
                                    </span>
                                </tooltip>
                                <tooltip message="Activate" v-else>
                                    <span class="table--action-activate">
                                        <a @click="activateSslCertificate(ssl_certificate.id)"><span class="icon-check_circle"></span></a>
                                    </span>
                                </tooltip>
                            </template>
                            <tooltip message="Delete">
                                <span class="table--action-delete">
                                    <a @click="deleteSslCertificate(ssl_certificate.id)"><span class="fa fa-trash"></span></a>
                                </span>
                            </tooltip>
                        </template>
                    </td>
                </tr>
            </tbody>
        </table>

    </section>
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
                if(this.siteId) {
                    this.$store.dispatch('getSslCertificates', this.siteId);
                }

                if(this.serverId) {
                    this.$store.dispatch('getServerSslCertificates', this.serverId);
                }

            },
            installCertificate() {
                if(this.siteId) {
                    this.$store.dispatch('installSslCertificate', {
                        site_id: this.siteId,
                        type : this.form.type,
                        domains: this.form.domains,
                        private_key : this.form.private_key,
                        certificate : this.form.certificate,
                    }).then((data) => {
                        if(data) {
                            this.resetForm()
                        }
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('installServerSslCertificate', {
                        type : this.form.type,
                        server_id: this.serverId,
                        domains: this.form.domains,
                        private_key : this.form.private_key,
                        certificate : this.form.certificate,
                    }).then((data) => {
                        if(data) {
                            this.resetForm()
                        }
                    })
                }

            },
            activateSslCertificate : function(ssl_certificate_id) {
                if(this.siteId) {
                    this.$store.dispatch('updateSslCertificate', {
                        active : true,
                        site : this.siteId,
                        ssl_certificate : ssl_certificate_id
                    })
                }
            },
            deactivateSslCertificate : function(ssl_certificate_id) {
                if(this.siteId) {
                    this.$store.dispatch('updateSslCertificate', {
                        active : false,
                        site : this.siteId,
                        ssl_certificate : ssl_certificate_id
                    })
                }
            },
            deleteSslCertificate: function (ssl_certificate_id) {

                if(this.siteId) {
                    this.$store.dispatch('deleteSslCertificate', {
                        site : this.siteId,
                        ssl_certificate : ssl_certificate_id
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('deleteServerSslCertificate', {
                        server : this.serverId,
                        ssl_certificate : ssl_certificate_id
                    })
                }

            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\SslCertificate', id);
            },
            retryInstall(domains) {

                if(this.siteId) {
                    this.$store.dispatch('installSslCertificate', {
                        site_id: this.siteId,
                        domains: domains,
                        type : 'Let\'s Encrypt'
                    }).then(() => {
                        this.$data.form = this.$options.data().form
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('installServerSslCertificate', {
                        domains: domains,
                        type : 'Let\'s Encrypt',
                        server_id: this.serverId,
                    }).then(() => {
                        this.$data.form = this.$options.data().form
                    })
                }

            },
            resetForm() {
                this.$data.form = this.$options.data().form
            }
        },
        computed: {
            siteId() {
                let siteId = this.$route.params.site_id
                if(siteId) {
                    this.form.type = 'Let\'s Encrypt'
                }
                return siteId
            },
            serverId() {
                let serverId = this.$route.params.server_id
                if(serverId) {
                    this.form.type = 'existing'
                }
                return serverId
            },
            ssl_certificates() {
                return this.$store.state.siteSslCertificatesStore.ssl_certificates;
            }
        }
    }
</script>