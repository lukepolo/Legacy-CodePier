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
            <server-header></server-header>
            <div class="section-content">
                <div class="container">

                    <transition>
                        <router-view name="nav"></router-view>
                    </transition>

                    <transition >
                        <router-view name="subNav">
                            <router-view></router-view>
                        </router-view>
                    </transition>

                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import LeftNav from '../../core/LeftNav.vue';
    import ServerHeader from './ServerHeader.vue';

    export default {
        components: {
            LeftNav,
            ServerHeader,
        },
        data() {
            return {
                transitionName : null
            }
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
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.$store.dispatch('user_servers/show', this.$route.params.server_id);
            },
        }
    }
</script>