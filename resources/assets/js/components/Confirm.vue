<template>
    <span class="confirm-container" @keyup.32.prevent @keyup.esc="close()">
        <button :class="confirm_class" @click="toggle()">
            <slot></slot>
        </button>
        <transition name="confirm">
            <div v-show="confirm" class="confirm-dialog" :class="confirm_position">
                <template v-if="confirm_with_text">
                    <h4 class="confirm-header">{{ confirm_message }}</h4>
                    <div class="confirm-content">
                        <p>Please confirm by typing in: {{ confirm_with_text }}</p>
                         <div class="flyform--group">
                            <form @submit.prevent.stop="confirmMethod">
                                <input ref="confirm_input" v-model="confirmedText" type="text" name="confirm-name" placeholder=" ">
                                <label for="confirm-name">Confirm</label>
                            </form>
                         </div>
                    </div>
                </template>
                <h4 class="confirm-header" v-if="message">{{ message }}</h4>
                <div class="confirm-content">
                    <slot name="form"></slot>
                </div>
                <div class="btn-footer">
                    <span class="btn btn-small" @click.stop.prevent="close()">{{ cancelText }}</span>
                    <slot name="confirm-button">
                        <button class="btn btn-small" :class="confirmButtonClass" @click.stop.prevent="confirmMethod">{{ confirmText }}</button>
                    </slot>
                </div>
            </div>
        </transition>
    </span>
</template>

<script>
export default {
  props: {
    params: {},
    dispatch: {},
    cancel_text: {},
    confirm_text: {},
    confirm_with_text: {},
    message: "",
    confirm_class: {
      default: "btn",
    },
    confirm_position: {
      default: "top",
    },
    confirm_message: {
      default: "Are you sure?",
    },
    confirm_btn: {
      default: "btn-danger",
    },
  },
  data() {
    return {
      confirm: false,
      confirmedText: "",
    };
  },
  watch: {
    confirm() {
      Vue.nextTick(() => {
        if (this.$refs.confirm_input) {
          this.$refs.confirm_input.focus();
        }
      });
    },
  },
  computed: {
    cancelText() {
      return "Cancel";
    },
    confirmText() {
      return this.confirm_text ? this.confirm_text : "Confirm";
    },
    textConfirmed() {
      if (this.confirm_with_text) {
        if (
          _.lowerCase(this.confirmedText) !==
          _.lowerCase(this.confirm_with_text)
        ) {
          return false;
        }
      }
      return true;
    },
    confirmButtonClass() {
      let classes = this.confirm_btn;
      if (!this.textConfirmed) {
        classes = classes + " btn-disabled";
      }
      return classes;
    },
  },
  methods: {
    toggle() {
      if (this.confirm) {
        this.confirm = false;
        return;
      }
      app.$emit("close-confirms");
      this.confirm = true;
    },
    close() {
      $(this.$el)
        .closest(".dropdown")
        .removeClass("open");
      this.confirm = false;
    },
    confirmMethod() {
      if (this.textConfirmed) {
        this.confirmedText = "";
        this.$store.dispatch(this.dispatch, this.params);
        this.close();
      }
    },
  },
  created() {
    app.$on("close-confirms", () => {
      this.confirm = false;
    });
  },
};
</script>
