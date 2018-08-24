<template>
    <span :is="tag" class="dropdown" :class="position" @click="show($event.target)">

        <slot name="header">
            <a href="#" @click.prevent class="dropdown--toggle">
                <span :class="icon"></span>
                <span class="muted" v-if="muted">{{ muted }} :</span>

                <template v-if="name && name.length > 18">
                    <tooltip :message="name" placement="bottom">
                        <span @click.stop="show($event.target)" class="text-clip">{{ name }}</span>
                    </tooltip>
                </template>
                <template v-else>
                    <span @click.stop="show($event.target)" class="text-clip">{{ name }}</span>
                </template>
            </a>
            <slot name="sub"></slot>
        </slot>

        <slot name="content" @click.stop="done">
            <div class="dropdown--menu">
                <slot></slot>
            </div>
        </slot>
    </span>
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
  methods: {
    show(target) {
      if (
        this.open
        // &&
        // !this.isTag(target, "textarea") &&
        // !this.isTag(target, "input") &&
        // !this.isTag(target, "select") &&
        // !this.isTag(target, "option")
      ) {
        this.open = false;

        return this.open;
      }

      this.$emit("close-dropdowns");
      this.open = true;
    },
  },
  created() {
    this.$on("close-dropdowns", () => {
      this.open = false;
    });
  },
  slots() {
    return this.$slots;
  },
});
</script>
