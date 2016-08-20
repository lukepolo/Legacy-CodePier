<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <h3 class="section-header primary">Site Repository</h3>
            <div class="section-content" v-if="site">
                <div class="container">
                    <site-nav></site-nav>
                    <form @submit.prevent="installLetsEncryptCertificate">
                        Domains
                        <input type="text" v-model="domains" name="domains">
                        <button type="submit">Install Let's Encrypt Certificate</button>
                    </form>

                    <p v-for="ssl_certificate in ssl_certificates">
                        {{ ssl_certificate.type }} : {{ ssl_certificate.domains }}
                        <a href="#" v-if="ssl_certificate.active">Deactivate</a>
                        <a href="#" v-else>Activate</a>
                        <a @click="deleteSslCertivicate(ssl_certificate.id)" href="#">Delete</a>
                    </p>
                </div>
            </div>
        </section>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import SiteNav from './components/SiteNav.vue';

    export default {
        components: {
            SiteNav,
            LeftNav,
        },
        data () {
            return {
                domains: null,
            }
        },
        computed: {
            site: () => {
                return siteStore.state.site;
            },
            ssl_certificates: () => {
                return siteStore.state.ssl_certificates;
            }
        },
        methods: {
            installLetsEncryptCertificate: function () {
                siteStore.dispatch('installLetsEncryptSslCertificate', {
                    site_id: this.site.id,
                    domains: this.domains
                })
            },
            deleteSslCertivicate: function (ssl_certificate_id) {
                siteStore.dispatch('deleteSslCertificate', ssl_certificate_id)
            }
        },
        mounted() {
            siteStore.dispatch('getSite', this.$route.params.site_id);
            siteStore.dispatch('getSslCertificates', this.$route.params.site_id);
        }
    }
</script>