<template>
    <div class="parent">

        <left-nav></left-nav>

        <section id="middle" class="section-column">

            <template>
                <h3 class="section-header primary">
                    <back></back>
                    My Account
                </h3>
            </template>

            <div class="section-content">
                <div class="container">
                    <div class="tab-container tab-left">
                        <router-view name="nav"></router-view>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active">
                                <router-view></router-view>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </div>

</template>

<script>
import LeftNav from "../../../components/LeftNav";

export default {
  components: {
    LeftNav
  }
};
</script>
