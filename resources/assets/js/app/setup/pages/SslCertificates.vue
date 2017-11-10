<template>
    <section>
        <form @submit.prevent="installCertificate">

            <div class="flyform--group">
                <label>Certificate Type</label>
            </div>

            <div class="grid-2">
                <div v-if="!serverId" class="flyform--group-radio">
                    <label>
                        <input name="type" type="radio" v-model="form.type" value="Let's Encrypt">
                        <span class="icon"></span>
                        Let's Encrypt
                    </label>
                </div>
                <div class="flyform--group-radio">
                    <label>
                        <input name="type" type="radio" v-model="form.type" value="existing">
                        <span class="icon"></span>
                        Existing Certificate
                    </label>
                </div>
            </div>

            <template v-if="form.type">
                <div class="flyform--group">
                    <input type="text" v-model="form.domains" name="domains" placeholder=" ">
                    <label for="domains">Domain(s)</label>
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
                    <button class="btn btn-primary" type="submit">Add Certificate</button>
                </div>
            </div>

        </form>

        <div v-if="ssl_certificates.length">
            <h3>SSL Certificates</h3>

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
                <tr v-for="ssl_certificate in ssl_certificates" :key="ssl_certificate.id">
                    <td>{{ ssl_certificate.domains }}</td>
                    <td>{{ ssl_certificate.type }}</td>
                    <td class="break-word">{{ ssl_certificate.cert_path }}</td>
                    <td class="break-word">{{ ssl_certificate.key_path }}</td>
                    <td class="table--action">
                        <template v-if="isRunningCommandFor(ssl_certificate.id)">
                            {{ isRunningCommandFor(ssl_certificate.id).status }}

                            <tooltip message="Delete">
                                <span class="table--action-delete">
                                    <a @click="deleteSslCertificate(ssl_certificate.id)"><span class="icon-trash"></span></a>
                                </span>
                            </tooltip>
                        </template>
                        <template v-else>
                            <template v-if="ssl_certificate.failed">
                                <tooltip message="Retry Install">
                                    <span class="table--action-retry">
                                        <a @click="retryInstall(ssl_certificate.domains)"><span class="icon-refresh"></span></a>
                                    </span>
                                </tooltip>
                            </template>
                            <template v-else>
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
                                    <a @click="deleteSslCertificate(ssl_certificate.id)"><span class="icon-trash"></span></a>
                                </span>
                            </tooltip>
                        </template>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <input type="hidden" v-if="site">
    </section>
</template>

<script>
    export default {
        data() {
            return {
                form  : this.createForm({
                    type : null,
                    domains : null,
                    private_key : null,
                    certificate : null,
                })
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
                    this.$store.dispatch('user_site_ssl_certificates/get', this.siteId);
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_ssl_certificates/get', this.serverId);
                }

            },
            installCertificate() {
                if(this.siteId) {
                    this.$store.dispatch('user_site_ssl_certificates/store', {
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
                    this.$store.dispatch('user_server_ssl_certificates/store', {
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
                    this.$store.dispatch('user_site_ssl_certificates/update', {
                        active : true,
                        site : this.siteId,
                        ssl_certificate : ssl_certificate_id
                    })
                }
            },
            deactivateSslCertificate : function(ssl_certificate_id) {
                if(this.siteId) {
                    this.$store.dispatch('user_site_ssl_certificates/update', {
                        active : false,
                        site : this.siteId,
                        ssl_certificate : ssl_certificate_id
                    })
                }
            },
            deleteSslCertificate: function (ssl_certificate_id) {

                if(this.siteId) {
                    this.$store.dispatch('user_site_ssl_certificates/destroy', {
                        site : this.siteId,
                        ssl_certificate : ssl_certificate_id
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_ssl_certificates/destroy', {
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
                    this.$store.dispatch('user_site_ssl_certificates/store', {
                        site_id: this.siteId,
                        domains: domains,
                        type : 'Let\'s Encrypt'
                    }).then(() => {
                        this.form.reset()
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_ssl_certificates/store', {
                        domains: domains,
                        type : 'Let\'s Encrypt',
                        server_id: this.serverId,
                    }).then(() => {
                        this.form.reset()
                    })
                }

            },
            resetForm() {
                this.form.reset()
                if(this.siteId) {
                    this.form.type = 'Let\'s Encrypt'
                    this.form.domains = this.site.domain
                } else {
                    this.form.type = 'existing'
                }
            }
        },
        computed: {
            site() {
                let site = this.$store.state.user_sites.site
                if(site) {
                    this.form.domains = site.domain
                }
                return site
            },
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
                return this.$store.state.user_site_ssl_certificates.ssl_certificates;
            }
        }
    }
</script>