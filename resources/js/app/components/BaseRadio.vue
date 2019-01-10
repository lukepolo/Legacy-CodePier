<template>
  <div class="flyform--group-radio">
    <tooltip
      size="medium"
      placement="top-right"
      :messsage="tooltip"
      v-if="tooltip"
    >
      <span class="fa fa-info-circle"></span>
    </tooltip>
    <label>
      <input
        :id="name"
        :name="name"
        type="radio"
        v-bind="$attrs"
        :value="value"
        :tabindex="tabindex"
        @change="selectValue"
      />
      <span class="icon"></span> {{ label }}
      <template v-if="description">
        <br />
        <small>{{ description }}</small>
      </template>
    </label>
  </div>
</template>

<script>
import Vue from "vue";
export default Vue.extend({
  model: {
    prop: "checked",
    event: "change",
  },
  props: {
    checked: {
      required: true,
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
    description: {
      type: String,
      required: false,
    },
    tabindex: {
      default: 0,
      type: Number,
    },
    tooltip: {
      required: false,
    },
    validation: {
      required: false,
    },
  },
  methods: {
    selectValue() {
      console.info(this);
      this.$emit("change", this.value);
    },
  },
});
</script>
