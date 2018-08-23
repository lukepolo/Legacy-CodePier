<template>
    <span class="btn btn-small clipboard" :data-clipboard-text="data">
        <span class="icon-clipboard"></span>
    </span>
</template>

<script>
import ClipboardJS from "clipboard";
export default {
  props: ["data"],
  created() {
    new ClipboardJS(".clipboard", {
      text: this.data,
    });
  },
};
</script>
