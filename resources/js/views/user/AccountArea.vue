<template>
    <section id="middle" class="column">
        <template>
            <h3 class="column--header primary">
                My Account
            </h3>
        </template>
        <div class="column--content">
            <div class="container">
                <div class="tab-container tab-left">
                    <account-nav></account-nav>
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active">
                            <router-view></router-view>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
