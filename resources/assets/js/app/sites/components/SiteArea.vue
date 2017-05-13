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

                    <template v-if="site && site.repository">
                        <transition>
                            <router-view name="nav"></router-view>
                        </transition>

                        <transition >
                            <router-view name="subNav">
                                <router-view></router-view>
                            </router-view>
                        </transition>
                    </template>
                    <template v-else>
                        <h3>Repository Informationsit</h3>
                        <router-view></router-view>
                    </template>
                </div>
            </div>
        </section>

        <servers></servers>
    </div>
</template>

<script>
    import SiteNav from './SiteNav.vue';
    import LeftNav from '../../core/LeftNav.vue';
    import Servers from './Servers.vue';
    import SiteHeader from './SiteHeader.vue';

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
                this.$store.dispatch('getSite', this.$route.params.site_id);
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site
            }
        }
    }
</script>