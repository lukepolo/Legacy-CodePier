<template>
    <div class="flyform--group">
        <tooltip size="medium" placement="top-right" :messsage="tooltip" v-if="tooltip">
            <span class="fa fa-info-circle"></span>
        </tooltip>
           <template>
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
               // TODO - put area back in
           </template>
        <label :class="{ 'flyform--group-iconlabel' : tooltip }" :for="name" v-if="label">{{ label }}</label>
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
      console.info("test");
      this.$emit("input", $event.target.value);
    };
  },
  computed: {
    componentType() {
      return this.type === "textarea" ? "textarea" : "input";
    },
  },
});
</script>
