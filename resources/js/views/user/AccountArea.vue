<template>
    <section id="middle" class="section-column">
        <template>
            <h3 class="section-header primary">
                My Account
            </h3>
        </template>
        <div class="section-content">
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
import AccountNav from "./components/AccountNav";
export default Vue.extend({
  components: {
    AccountNav,
  },
});
</script>
