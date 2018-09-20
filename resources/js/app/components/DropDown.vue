<template>
    <div :is="tag" class="dropdown" :class="dropDownClasses" @click="toggle">
        <slot name="header">
            <div @click.prevent class="dropdown--toggle">
                <span :class="icon"></span>
                <span class="muted" v-if="muted">{{ muted }} :</span>

                <template v-if="name && name.length > 18">
                    <tooltip :message="name" placement="bottom">
                        <span class="text-clip">{{ name }}</span>
                    </tooltip>
                </template>
                <template v-else>
                    <span class="text-clip">{{ name }}</span>
                </template>
            </div>
            <slot name="sub"></slot>
        </slot>

        <slot name="content" @click.stop="done">
            <div class="dropdown--menu">
                <slot></slot>
            </div>
        </slot>
    </div>
</template>

<script>
import Vue from "vue";
export default Vue.extend({
  props: {
    position: {
      default: "bottom left",
    },
    tag: {
      default: "div",
    },
    name: {
      default: null,
    },
    muted: {
      default: null,
    },
    icon: {
      default: null,
    },
  },
  data() {
    return {
      open: false,
    };
  },
  created() {
    document.addEventListener("click", this.close);
  },
  methods: {
    toggle() {
      this.$nextTick(() => {
        this.open = true;
      });
    },
    close() {
      if (this.open) {
        this.$nextTick(() => {
          this.open = false;
        });
      }
    },
  },
  computed: {
    dropDownClasses() {
      return {
        open: this.open,
        position: this.position,
      };
    },
  },
  beforeDestroy() {
    document.removeEventListener("click", this.close);
  },
});
</script>
