<template>
  <form @submit.prevent="action">
    <div class="flyform--content"><slot></slot></div>
    <div class="flyform--footer">
      <div class="flyform--footer-btns"><slot name="buttons"></slot></div>
      <div class="flyform--footer-links"><slot name="links"></slot></div>
    </div>
  </form>
</template>

<script>
import Vue from "vue";
export default Vue.extend({
  props: {
    action: {
      required: true,
    },
  },
});
</script>
