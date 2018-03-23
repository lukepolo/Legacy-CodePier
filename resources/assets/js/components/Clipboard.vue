<template>
    <span class="btn btn-small clipboard" :data-clipboard-text="data">
        <span class="icon-clipboard"></span>
    </span>
</template>

<script>
export default {
  props: ["data"],
  created() {
    new Clipboard(".clipboard", {
      text: this.data,
    });
  },
};
</script>
