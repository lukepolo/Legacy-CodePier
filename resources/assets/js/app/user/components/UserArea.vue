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

            <template>
                <h3 class="section-header primary">
                    My Account
                </h3>
            </template>

            <div class="section-content">
                <div class="container">

                    <div class="tab-container tab-left">

                        <transition >
                            <router-view name="nav"></router-view>
                        </transition>

                        <transition >
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active">
                                    <router-view></router-view>
                                </div>
                            </div>
                        </transition>

                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import LeftNav from '../../core/LeftNav.vue';

    export default {
        data() {
            return {
                transitionName : null
            }
        },
        components: {
            LeftNav,
        },
        watch: {
            '$route' (to, from) {
                const toDepth = to.path.split('/').length
                const fromDepth = from.path.split('/').length
//                this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
                this.transitionName = 'bounce';
            }
        }
    }
</script>