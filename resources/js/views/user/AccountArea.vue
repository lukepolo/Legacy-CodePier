<template>
    <section id="middle" class="column">

    </section>
</template>

<script>
import Vue from "vue";
import AccountNav from "./components/AccountNav.vue";

// const SubscriptionStore = () =>
//   import("@store/subscriptions/SubscriptionStore");

export default Vue.extend({
  components: {
    AccountNav,
  },
  // beforeRouteEnter(to, from, next) {
  //   SubscriptionStore().then((store) => {
  //     $app.make("storeService").registerStore(store.default);
  //     next();
  //   });
  // },
});
</script>
