<template>
    <div class="flyform--group">
        <slot>
          <input
              :id="name"
              :name="name"
              :type="type"
              v-bind="$attrs"
              :value="value"
              :tabindex="tabindex"
              :class="{ 'someClass' : !doesAppendExist, 'someOtherClass' : doesAppendExist }"
              @input="$emit('input', $event.target.value)"
              placeholder=" "
          >
          <label :for="name" v-if="label">{{ label }}</label>
        </slot>
          <div class="some-class-you-need-for-appending" v-if="doesAppendExist">
            <slot name="append"></slot>
          </div>
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
  computed: {
    doesAppendExist() {
      return Object.keys(this.$slots).indexOf("append") >= 0;
    },
  },
});
</script>
