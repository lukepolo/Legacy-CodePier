<template>
    <div class="parent">
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <site-progress></site-progress>
            Create site here
        </section>
    </div>
</template>

<script>
    import LeftNav from "../../../components/LeftNav";
    export default {
        components : {
          LeftNav,
        }
    }
</script>