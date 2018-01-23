<template>
    <transition name="collapse"
        v-on:before-enter="setup"
        v-on:enter="enter"
        v-on:after-enter="done"

        v-on:before-leave="setup"
        v-on:leave="leave"
        v-on:after-leave="done"

        v-bind:css="false"
    >
        <slot></slot>
    </transition>
</template>

<script>
export default {
  data() {
    return {
      collapsing: false,
      height: null,
      transitionSpeed: ".35"
    };
  },
  methods: {
    setup(el) {
      el.style.overflow = "hidden";
      el.style.position = "initial";
      el.style.transition = "all " + this.transitionSpeed + "s ease";
    },
    enter(el, done) {
      this.height = el.offsetHeight;
      el.style.height = 0;

      setTimeout(() => {
        el.style.height = this.height + "px";
      }, 0.00000001);

      setTimeout(function() {
        done();
      }, this.transitionSpeed * 1000);
    },
    leave(el, done) {
      el.style.height = this.height + "px";

      setTimeout(() => {
        el.style.height = 0;
      }, 0.00000001);

      setTimeout(function() {
        done();
      }, this.transitionSpeed * 1000);
    },
    done(el) {
      el.style.height = null;
      el.style.overflow = null;
      el.style.position = null;
      el.style.transition = null;
    }
  }
};
</script>
