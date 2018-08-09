<template>
    <div class="flyform--group">
        <input
            :id="name"
            :name="name"
            :type="type"
            v-bind="$attrs"
            :value="value"
            :tabindex="tabindex"
            @input="$emit('input', $event.target.value)"
            placeholder=" "
        >
        <label :for="name" v-if="label">{{ label }}</label>
    </div>
</template>

<script>
import Vue from "vue";
export default Vue.extend({
  props: {
    type: {
      type: String,
      default: "text",
    },
    value: {
      required: true,
    },
    name: {
      required: true,
      type: String,
    },
    label: {
      type: String,
      required: true,
    },
    tabindex: {
      default: 0,
      type: Number,
    },
    validation: {
      required: false,
    },
  },
});
</script>
