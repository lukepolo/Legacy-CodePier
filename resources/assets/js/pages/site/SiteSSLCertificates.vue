<template>
    <div v-if="site">
        <div class="jcf-form-wrap">
            <form @submit.prevent="installLetsEncryptCertificate">
                <h3>SSL Certificates</h3>
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


        <p v-for="ssl_certificate in ssl_certificates">
            {{ ssl_certificate.type }} : {{ ssl_certificate.domains }} : {{ ssl_certificate.cert_path }} :
            {{ ssl_certificate.key_path }}
            <a href="#" v-if="ssl_certificate.active">Deactivate</a>
            <a href="#" v-else>Activate</a>
            <a @click="deleteSslCertificate(ssl_certificate.id)" href="#">Delete</a>
        </p>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                domains: null,
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
                this.$store.dispatch('getSite', this.$route.params.site_id);
                this.$store.dispatch('getSslCertificates', this.$route.params.site_id);
            },
            installLetsEncryptCertificate() {
                this.$store.dispatch('installSslCertificate', {
                    site_id: this.site.id,
                    domains: this.domains,
                    type : 'Let\'s Encrypt'
                })
            },
            deleteSslCertificate: function (ssl_certificate_id) {
                this.$store.dispatch('deleteSslCertificate', {
                    site : this.site.id,
                    certificate : ssl_certificate_id,
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