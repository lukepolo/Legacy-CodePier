<template>
    <span> {{ text }} </span>
</template>

<script>
export default {
  props: {
    time: {},
  },
  mounted() {
    this.setCurrentTime();
    this.interval = setInterval(() => {
      this.update();
    }, 1000);
  },
  data() {
    return {
      interval: null,
      currentTime: null,
    };
  },
  watch: {
    time: function() {
      this.setCurrentTime();
    },
  },
  methods: {
    update() {
      Vue.set(this.currentTime, moment());
    },
    setCurrentTime() {
      let time = this.time;

      if (!moment.isMoment(time)) {
        time = this.parseDate(time);
      }

      this.currentTime = time;
    },
  },
  beforeDestroy() {
    clearInterval(this.interval);
  },
  computed: {
    text() {
      if (this.currentTime) {
        return this.currentTime
          .fromNow()
          .replace("ute", "")
          .replace("ago", "");
      }
    },
  },
};
</script>
