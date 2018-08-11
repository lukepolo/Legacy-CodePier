<template>
    <section>
        <div class="flyform--group">
            <div :id="cardType + '-card-element'"></div>
            <div id="card-errors" class="alert alert-error" role="alert" v-if="stripe.error">
                {{ stripe.error }}
            </div>
        </div>
    </section>
</template>

<script>
export default {
  props: ["cardType", "card", "error", "instance"],
  data() {
    return {
      stripe: {
        card: null,
        error: null,
        instance: null,
      },
    };
  },
  watch: {
    stripe: {
      deep: true,
      handler: function() {
        this.$emit("update:card", this.stripe.card);
        this.$emit("update:error", this.stripe.error);
        this.$emit("update:instance", this.stripe.instance);
      },
    },
  },
  mounted() {
    this.stripe.instance = Stripe(window.Laravel.stripeKey);

    this.stripe.card = this.stripe.instance.elements().create("card", {
      style: {
        base: {
          color: "#fff",
          iconColor: "#fff",
          lineHeight: "24px",
          fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
          fontSmoothing: "antialiased",
          fontSize: "16px",
        },
        invalid: {
          color: "#F4645F",
          iconColor: "#F4645F",
        },
      },
    });

    this.stripe.card.mount("#" + this.cardType + "-card-element");

    this.stripe.card.addEventListener("change", (event) => {
      this.stripe.error = null;
      if (event.error) {
        this.stripe.error = event.error.message;
      }
    });
  },
};
</script>
