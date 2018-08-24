<template>
    <div id="app" v-if="user">
        <system-alerts></system-alerts>
        <navigation></navigation>
        <left-nav></left-nav>
        <router-view class="view"></router-view>

        <div id="right">
          right
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut aliquet vehicula tortor vitae consequat. Curabitur non bibendum ex. Nunc nec augue sagittis, faucibus lorem vitae, congue augue. Phasellus semper libero ligula, a cursus nisl imperdiet nec. In vitae dictum diam. Nullam sagittis convallis ultricies. Sed a eros viverra, scelerisque nibh id, lacinia tortor. Cras a velit ex. In gravida non tortor congue scelerisque. Phasellus dignissim vehicula dui vitae eleifend.

Nam ac nulla non lorem mollis blandit a ut urna. Integer pharetra turpis eget lorem gravida pellentesque. Vestibulum at consequat nisi. Morbi ut placerat odio. Ut tincidunt eget odio congue tristique. Curabitur luctus interdum magna non ultricies. Maecenas tortor nulla, sodales eget pellentesque vitae, sollicitudin et odio. Sed quis volutpat erat. In eget porta lorem, eget aliquam nisi. Pellentesque in mauris molestie, auctor turpis in, faucibus ex. Nam maximus faucibus eros a semper. Curabitur risus tortor, consectetur vel iaculis a, cursus a est.

Ut malesuada nibh eu suscipit mollis. Nunc sed venenatis tellus, lobortis varius purus. Pellentesque et lectus nec nunc egestas ornare. Nam efficitur elit convallis vestibulum sodales. Duis feugiat ex rutrum, congue nulla quis, blandit mauris. Ut ac posuere turpis. Suspendisse placerat mattis magna, vel dapibus quam convallis et. Donec ut egestas orci. Aenean egestas bibendum justo, in cursus erat. Praesent auctor pulvinar felis a sagittis. Curabitur vel tortor augue. In interdum neque at lectus sollicitudin fermentum. Curabitur at accumsan neque. Nulla convallis sit amet lorem placerat maximus. Praesent cursus tellus et lectus posuere elementum sagittis id mi. Donec vel vehicula libero, a ullamcorper turpis.

Vivamus gravida consectetur purus, eget tempor purus elementum ac. Donec quis scelerisque arcu, eget auctor sapien. Nulla feugiat vel sem nec tincidunt. Phasellus quis nisi laoreet, eleifend lorem vitae, ultricies nisl. Vestibulum viverra viverra nibh, id rhoncus ante vulputate quis. Quisque volutpat lobortis erat, quis blandit ante pretium eu. Duis mattis ullamcorper massa in molestie. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vivamus fringilla nisi at ex placerat ultrices. Donec ultrices lorem vitae nunc ultrices, condimentum dapibus sem feugiat. Pellentesque sit amet purus vitae sapien rhoncus aliquam. In at turpis vitae neque vehicula molestie. Nullam feugiat eget urna nec aliquet. Mauris eleifend nunc sapien, at eleifend quam aliquet ultrices.

Sed blandit eros nibh, ac suscipit turpis venenatis ac. Donec eu mi quis diam porta rutrum. Nulla eu orci nec felis tristique fermentum a accumsan dolor. Aenean eu est ut velit scelerisque efficitur sit amet at ligula. Integer a faucibus lectus. Vivamus tincidunt sollicitudin elit. Vestibulum elementum risus id porttitor malesuada.
        </div>
        <footer class="events">
          footer
        </footer>

        <!--<events-bar v-if="hasSites"></events-bar>-->
        <!-- <announcements></announcements> -->
        <!-- <portal-target name="modal" slim></portal-target> -->
    </div>
</template>

<script>
import Vue from "vue";
import LeftNav from "./components/LeftNav";
import Navigation from "./components/Navigation";
import SystemAlerts from "./components/SystemAlerts";
import Announcements from "./components/Announcements";

export default Vue.extend({
  components: {
    LeftNav,
    Navigation,
    SystemAlerts,
    Announcements,
  },
  mounted() {
    this.$store.dispatch("user/piles/get");
    this.$store.dispatch("user/sites/get");
  },
});
</script>
