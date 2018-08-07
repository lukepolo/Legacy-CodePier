<template>
    <div class="parent">

        <left-nav></left-nav>

        <section id="middle" class="section-column">
            <server-header></server-header>
            <div class="section-content">
                <div class="container">

                    <transition>
                        <router-view name="nav"></router-view>
                    </transition>

                    <transition >
                        <router-view name="subNav">
                            <router-view></router-view>
                        </router-view>
                    </transition>

                </div>
            </div>
        </section>
    </div>
</template>

<script>
import LeftNav from "../../../components/LeftNav";
import ServerHeader from "./ServerHeader";

export default {
  components: {
    LeftNav,
    ServerHeader
  },
  data() {
    return {
      transitionName: null
    };
  },
  watch: {
    $route(to, from) {
      const toDepth = to.path.split("/").length;
      const fromDepth = from.path.split("/").length;
      //                this.transitionName = toDepth < fromDepth ? 'slide-right' : 'slide-left'
      this.transitionName = "bounce";
      this.fetchData();
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      let serverId = this.$route.params.server_id;
      if (!this.server || this.server.id !== parseInt(serverId)) {
        // TODO - we can remove after we put events out
        this.$store.dispatch("user_servers/show", serverId);
      }
    }
  },
  computed: {
    server() {
      return this.$store.state.user_servers.server;
    }
  }
};
</script>
