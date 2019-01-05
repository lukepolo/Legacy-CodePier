<template>
  <div>
    this is the area?
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  computed: {
    site() {
      return this.$store.getters["user/sites/show"](this.$route.params.site);
    },
  },
};
</script>
