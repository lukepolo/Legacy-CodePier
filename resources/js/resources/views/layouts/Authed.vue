<template>
    <div id="app" v-if="user">
        <notifications></notifications>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
  mounted() {
    this.$store.dispatch("user/piles/get");
  },
};
</script>
