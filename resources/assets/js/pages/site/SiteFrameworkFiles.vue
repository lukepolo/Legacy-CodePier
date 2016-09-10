<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <div class="container">
                <site-nav></site-nav>
                <section>
                    <div v-for="(files, service) in editable_files">
                        <template v-for="file in files">
                            {{ sectionTitle(service) }}
                            <server-file :server="server" :file="file"></server-file>
                        </template>
                    </div>
                </section>
            </div>
        </section>
        <servers></servers>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import SiteNav from './components/SiteNav.vue';
    import Servers from './components/Servers.vue';
    import SiteHeader from './components/SiteHeader.vue';

    export default {
        components: {
            SiteHeader,
            SiteNav,
            LeftNav,
            Servers
        },
        computed : {
            editable_files : []
        }
    }
</script>