<template>
    <div class="flyform--group-checkbox">
        <label :for="name" v-if="label">
            <input
                :id="name"
                :name="name"
                type="checkbox"
                v-bind="$attrs"
                :value="value"
                :tabindex="tabindex"
                :checked="value === true || value === 1"
                @change="$emit('input', $event.target.checked)"
                placeholder=" "
            >
            <span class="icon"></span>
            Enable
        </label>
    </div>
</template>

<script>
import Vue from "vue";
export default Vue.extend({
  props: {
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
