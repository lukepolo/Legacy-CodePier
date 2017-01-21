<template>
    <div class="parent">
        <section id="middle" class="section-column">
            <div class="section-content">
                <div class="container">

                    <transition >
                        <router-view name="nav"></router-view>
                    </transition>

                    <transition >
                        <router-view></router-view>
                    </transition>

                </div>
            </div>
        </section>
    </div>
</template>

<script>
    export default {
        components: {
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
            }
        }
    }
</script>