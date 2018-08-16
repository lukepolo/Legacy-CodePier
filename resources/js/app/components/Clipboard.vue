<template>
    <span class="btn btn-small clipboard" :data-clipboard-text="data">
        <span class="icon-clipboard"></span>
    </span>
</template>

<script>
import Clipboard from "clipboard";
export default {
  props: ["data"],
  created() {
    new Clipboard(".clipboard", {
      text: this.data,
    });
  },
};
</script>
