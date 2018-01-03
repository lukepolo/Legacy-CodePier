<template>
    <span
        :class="[
        'hint--'+type,
        'hint--'+size,
        'hint--'+placement,
        'hint--'+placementComputed,
        {
            'hint--rounded': this.rounded,
            'hint--always': this.always,
            'hint--no-animate': this.noAnimate
        }]"
        :aria-label="message"
        @mouseover="startDelay"
        @mouseleave="clearDelay"
    >
        {{ type }}
        <slot></slot>
    </span>
</template>

<script>
export default {
  props: {
    type: String,
    size: String,
    always: Boolean,
    rounded: Boolean,
    noAnimate: Boolean,
    message: {
      type: String,
      default: ""
    },
    placement: {
      type: String,
      default: "top"
    },
    delay: {
      default: 0
    }
  },
  data() {
    return {
      show: false,
      delayTimeout: null
    };
  },
  methods: {
    startDelay: function() {
      this.delayTimeout = setTimeout(() => {
        this.show = true;
      }, this.delay * 1000);
    },
    clearDelay: function() {
      clearTimeout(this.delayTimeout);
      this.show = false;
    }
  },
  computed: {
    placementComputed() {
      if (this.show === true || this.delay === 0) {
        return;
      }

      return "none";
    }
  }
};
</script>
