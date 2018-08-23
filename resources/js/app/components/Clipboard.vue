<template>
    <span class="btn btn-small clipboard" :data-clipboard-text="data">
        <span class="icon-clipboard"></span>
    </span>
</template>

<script>
<<<<<<< HEAD:resources/js/app/components/Clipboard.vue
<<<<<<< HEAD:resources/old_js/components/Clipboard.vue
import ClipboardJS from "clipboard";
=======
import Clipboard from "clipboard";
>>>>>>> my account page settled up:resources/js/app/components/Clipboard.vue
=======
import Clipboard from "clipboard";
>>>>>>> acc018fe8b80ac9df1c2dfd7edb1f8ff56c4d148:resources/js/app/components/Clipboard.vue
export default {
  props: ["data"],
  created() {
    new ClipboardJS(".clipboard", {
      text: this.data,
    });
  },
};
</script>
