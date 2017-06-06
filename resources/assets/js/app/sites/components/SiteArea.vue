<style>
    .bounce-enter-active {
        animation: bounce-in .5s;
    }
    .bounce-leave-active {
        animation: bounce-out .5s;
    }
    @keyframes bounce-in {
        0% {
            transform: scale(0);
        }
        50% {
            transform: scale(1.5);
        }
        100% {
            transform: scale(1);
        }
    }
    @keyframes bounce-out {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.5);
        }
        100% {
            transform: scale(0);
        }
    }
</style>

<template>
    <div class="parent">

        <left-nav></left-nav>

        <section id="middle" class="section-column">
            <site-header></site-header>

            <div class="section-content">
                <div class="container">

                    <template v-if="site">
                        <transition v-if="site.repository">
                            <router-view name="nav"></router-view>
                        </transition>
                        <transition >
                            <router-view name="subNav">
                                <router-view></router-view>
                            </router-view>
                        </transition>
                    </template>

                </div>
            </div>
        </section>

        <servers></servers>
    </div>
</template>

<script>
    import SiteNav from './SiteNav.vue'
    import Servers from './Servers.vue'
    import SiteHeader from './SiteHeader.vue'
    import LeftNav from '../../../components/LeftNav.vue'

    export default {
        data() {
            return {
                transitionName : null
            }
        },
        components: {
            SiteNav,
            LeftNav,
            Servers,
            SiteHeader,
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route' (to, from) {
                const toDepth = to.path.split('/').length
                const fromDepth = from.path.split('/').length
//                this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
                this.transitionName = 'bounce';
                this.fetchData()
            }
        },
        methods: {
            fetchData() {
                let siteId = this.$route.params.site_id
                if(!this.site || this.site.id !== parseInt(siteId)) {
                    this.$store.dispatch('user_sites/show', siteId)
                }
            }
        },
        computed: {
            site() {
                return this.$store.state.user_sites.site
            }
        }
    }
</script>