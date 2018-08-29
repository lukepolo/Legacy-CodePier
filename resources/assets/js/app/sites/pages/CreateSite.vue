<template>
    <div class="parent">
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <site-progress></site-progress>
            <site-repository></site-repository>
        </section>
    </div>
</template>

<script>
    import SiteRepository from './SiteRepository'
    import LeftNav from "../../../components/LeftNav";
    export default {
        components : {
          LeftNav,
          SiteRepository
        },
        computed : {
            pileId() {
                return this.$route.params.pile_id;
            }
        }
    }
</script>