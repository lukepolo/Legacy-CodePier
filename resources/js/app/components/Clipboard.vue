<template>
    <span class="btn btn-small clipboard" :data-clipboard-text="data">
        <span class="icon-clipboard"></span>
    </span>
</template>

<script>
import Vue from "vue";
import ClipboardJS from "clipboard";
export default Vue.extend({
  props: ["data"],
  created() {
    new ClipboardJS(".clipboard", {
      text: this.data,
    });
  },
});
</script>
