<template>
    <div class="flyform--group-checkbox">
        <tooltip size="medium" placement="top-right" :messsage="tooltip" v-if="tooltip">
            <span class="fa fa-info-circle"></span>
        </tooltip>
        <label :for="name">
            <input
                :id="name"
                :name="name"
                type="checkbox"
                v-bind="$attrs"
                :value="value"
                :tabindex="tabindex"
                @change="updateValues"
                :checked="isChecked"
            >
            <span class="icon"></span>
            {{ label }}
            <template v-if="description">
                <br>
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
      required: false,
      default: true,
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
    updateValues() {
      if (Array.isArray(this.checked)) {
        if (!this.isChecked) {
          this.checked.push(this.value);
        } else {
          this.$emit(
            "change",
            this.checked.filter((checkedValue) => {
              return checkedValue !== this.value;
            }),
          );
        }
      } else {
        this.$emit("change", this.checked != true);
      }
    },
  },
  computed: {
    isChecked() {
      if (Array.isArray(this.checked)) {
        return this.checked.find((checkedValue) => {
          return checkedValue == this.value;
        });
      }
      return this.checked;
    },
  },
});
</script>
