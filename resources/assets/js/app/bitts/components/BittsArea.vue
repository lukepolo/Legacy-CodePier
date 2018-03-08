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

        <section id="right" class="section-column" v-if="bitt">
            <router-view name="right"></router-view>
        </section>

    </div>
</template>

<script>
export default {
  computed: {
    bitt() {
      return this.$store.state.bitts.bitt;
    }
  }
};
</script>
