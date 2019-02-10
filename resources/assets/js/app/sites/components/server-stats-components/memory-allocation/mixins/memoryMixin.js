export default {
  methods : {
    memoryUsage(stats) {
      return (
        100 - (
          stats.available /
          stats.total *
          100
        )
      );
    }
  }
}
