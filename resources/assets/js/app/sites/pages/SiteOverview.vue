<template>
    <div v-if="site">

        <div>
            <h3>
                Health Check
            </h3>
            <p>
                https://codepier.io <i class="fa fa-check-circle"></i>
            </p>
        </div>

        <div>
            <h3>
                Repository / Deploy Branch
            </h3>
            <p>
                CodePier/CodePier - master
            </p>
        </div>

        <div>
            <h3>Recent deployments</h3>
            dpeloyment 12-24-197 took 100 seconds<br>
            dpeloyment 12-24-197 took 100 seconds<br>
            dpeloyment 12-24-197 took 100 seconds<br>
            dpeloyment 12-24-197 took 100 seconds<br>
            dpeloyment 12-24-197 took 100 seconds<br>
            dpeloyment 12-24-197 took 100 seconds<br>
            dpeloyment 12-24-197 took 100 seconds<br>
        </div>

        <div>
            <h3>Recent Commands</h3>
            fireawll rule installed<br>
            fireawll rule installed<br>
            fireawll rule installed<br>
            fireawll rule installed<br>
            fireawll rule installed<br>
            fireawll rule installed<br>
        </div>

        <div>
            <h3>
                DNS
                <i class="fa fa-refresh" @click="getDns(true)"></i>
            </h3>
            <template v-if="dns.host">
                Your site is pointed to : {{ dns.ip }}
            </template>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: this.createForm({
                    domain : null,
                })
            }
        },
        mounted() {
            this.fetchData()
            this.siteChange()
        },
        watch: {
            '$route' : 'fetchData',
            'site' : 'siteChange',
        },
        methods: {
            fetchData() {
                this.getDns()
            },
            siteChange() {

                this.form.empty()

                let site = this.site

                if (site && site.id === parseInt(this.$route.params.site_id)) {
                    if(site.repository) {
                        this.form.domain = site.domain
                    } else {
                        this.$router.push({ name: 'site_repository', params: { site_id: site.id }})
                    }
                }

                this.form.setOriginalData()

            },
            updateSite() {
                this.$store.dispatch('user_sites/update', {
                    site: this.site.id,

                });
            },
            getDns(refresh) {

                let data = {
                    site : this.$route.params.site_id,
                }

                if(refresh) {
                    data.refresh = true
                }

                this.$store.dispatch('user_site_dns/get', data)
            }
        },
        computed: {
            dns() {
                return this.$store.state.user_site_dns.dns
            },
            site() {
                return this.$store.state.user_sites.site
            },
        },
    }
</script>