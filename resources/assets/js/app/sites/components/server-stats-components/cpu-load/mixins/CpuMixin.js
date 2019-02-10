export default {
  getCpuLoad(load) {
    let loadPercent = load / this.stats.cpus * 100;
    return loadPercent > 100 ? 100 : loadPercent;
  },
}
