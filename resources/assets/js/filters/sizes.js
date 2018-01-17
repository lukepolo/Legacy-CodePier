import Vue from "vue";

Vue.filter("ram", value => {
  return value >= 1024 ? round(value / 1024, 2) + " GB" : value + " MB";
});

Vue.filter("diskSize", value => {
  return value >= 1024 ? round(value / 1024, 2) + " TB" : value + " GB";
});

function round(value, decimals) {
  return Number(Math.round(value + "e" + decimals) + "e-" + decimals);
}
