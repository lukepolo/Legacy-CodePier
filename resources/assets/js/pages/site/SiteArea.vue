<style>
    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s
    }
    .fade-enter, .fade-leave-active {
        opacity: 0
    }

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

            <transition name="bounce">
                <router-view name="nav"></router-view>
            </transition>

            <transition name="bounce">
                <router-view class="container"></router-view>
            </transition>

        </section>
        <servers></servers>
    </div>
</template>

<script>
    import SiteNav from './components/SiteNav.vue';
    import LeftNav from './../../core/LeftNav.vue';
    import Servers from './components/Servers.vue';
    import SiteHeader from './components/SiteHeader.vue';

    export default {
        components: {
            SiteNav,
            LeftNav,
            Servers,
            SiteHeader,
        }
    }
</script>