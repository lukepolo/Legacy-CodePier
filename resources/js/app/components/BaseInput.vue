<template>
    <div class="flyform--group" :class="{ 'flyform--group-prefix' : hasPrefix }">
        <tooltip size="medium" placement="top-right" :messsage="tooltip" v-if="tooltip">
            <span class="fa fa-info-circle"></span>
        </tooltip>
           <template v-if="type !== 'textarea'">
               <input
                   :id="name"
                   :name="name"
                   :type="type"
                   v-bind="$attrs"
                   :value="value"
                   :tabindex="tabindex"
                   v-on="$listeners"
                   placeholder=" "
               />
           </template>
            <template v-else>
                <textarea
                    :id="name"
                    :name="name"
                    :type="type"
                    v-bind="$attrs"
                    :value="value"
                    :tabindex="tabindex"
                    v-on="$listeners"
                    placeholder=" "
                ></textarea>
            </template>
        <label :class="{ 'flyform--group-iconlabel' : tooltip }" :for="name" v-if="label">{{ label }}</label>
        <div class="flyform--group-prefix-label">
            <slot name="prefix"></slot>
        </div>
        <div class="flyform--input-icon-right">
            <slot name="icon"></slot>
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
    tooltip: {
      required: false,
    },
  },
  created() {
    this.$listeners.input = ($event) => {
      this.$emit("input", $event.target.value);
    };
  },
  computed: {
    hasPrefix() {
      return this.$slots["prefix"];
    },
  },
});
</script>
