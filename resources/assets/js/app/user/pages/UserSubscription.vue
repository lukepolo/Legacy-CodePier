<template>
    <section>
        <template v-if="userSubscription">
            <div class="alert alert-warning" v-if="userSubscription.trial_ends_at">
                Your free trial ends on <strong>{{ parseDate(userSubscription.trial_ends_at).format('l') }}</strong>
            </div>

            <div class="alert alert-error" v-if="isCanceled">
                Your subscription has been canceled and will end on {{ parseDate(userSubscription.ends_at).format('l') }}
            </div>
        </template>

        <div class="pricing pricing-inapp">
            <div class="pricing--item" :class="{ selected : !this.userSubscription }">
                <div class="pricing--header">
                    <div class="pricing--header-name">Riggers</div>
                </div>
                <div class="pricing--features">
                    <div class="flyform--group-radio">
                        <label>
                            <input type="radio" name="plan" value="cancel" v-model="form.plan">
                            <span class="icon"></span>
                            Free
                        </label>
                    </div>
                </div>
            </div>
            <plans :selectedPlan.sync="form.plan" title="First Mate" type="firstmate"></plans>
            <plans :selectedPlan.sync="form.plan" title="Captain" type="captain"></plans>
        </div>

        <div class="text-center" v-if="!userSubscription">
            <h3>Start your 5 day free trial!</h3>
        </div>

        <form @submit.prevent="createSubscription" method="post">
            <div class="flyform--group coupon-form">
                <input type="text" name="coupon" v-model="form.coupon" placeholder=" ">
                <label for="coupon">Coupon Code</label>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">


                    <button class="btn btn-primary" :class="{ 'btn-disabled' : processing }">
                        <template v-if="isCanceled">
                            Resume Subscription
                        </template>
                        <template v-else-if="userSubscription">
                            Update Subscription
                        </template>
                        <template v-else>
                            Select Plan
                        </template>
                    </button>
                </div>

                <div class="flyform--footer-links">
                    <a class="text-error" @click="cancelSubscription" v-if="!isCanceled">
                        Cancel Subscription
                    </a>
                </div>
            </div>

            <br><br>

            <card v-if="!userSubscription" cardType="createCardForm" :card.sync="createCardForm.card" :error.sync="createCardForm.error" :instance.sync="createCardForm.instance"></card>

        </form>


        <form @submit.prevent="updateCard" method="post" v-if="currentCard">

            <h3 class="flex">
                Payment Method &nbsp;
                <div class="heading--btns">
                    <a class="btn-link" @click="showCreditForm = true">
                        <span class="icon-pencil"></span>
                    </a>
                </div>
            </h3>
            {{ currentCard.brand }} ending in {{ currentCard.last4 }}

            <div v-if="showCreditForm">
                <card cardType="updateCardForm" :card.sync="updateCardForm.card" :error.sync="updateCardForm.error" :instance.sync="updateCardForm.instance"></card>
            </div>

            <div v-if="showCreditForm" class="flyform--footer">
                <div class="flyform--footer-btns">
                    <span class="btn" @click="showCreditForm = false">Cancel</span>
                    <button class="btn btn-primary" :class="{ 'btn-disabled' : processing }">Update Card</button>
                </div>
            </div>
        </form>

        <template v-if="invoices.length">
            <table>
                <tr v-for="invoice in invoices">
                    <td> {{ parseDate(invoice.date.date).format('l') }}</td>
                    <td> ${{ invoice.total }}</td>
                    <td><a :href="downloadLink(invoice.id)">Download</a></td>
                </tr>
            </table>
        </template>
    </section>
</template>

<script>
import Card from "./../components/subscriptions/Card";
import Plans from "./../components/subscriptions/Plans";
export default {
  components: {
    Card,
    Plans
  },
  data() {
    return {
      card: null,
      showCreditForm: false,
      processing: false,
      form: this.createForm({
        plan: null,
        token: null,
        coupon : null,
        subscription: null,
      }),
      createCardForm: {
        card: null,
        error: null,
        instance: null
      },
      updateCardForm: {
        card: null,
        error: null,
        instance: null
      }
    };
  },
  watch: {
    userSubscription: function() {
      if (this.userSubscription) {
        this.form.plan = this.userSubscription.stripe_plan;
        this.form.subscription = this.userSubscription.id;
      } else {
        this.form.plan = "cancel";
      }
    }
  },
  created() {
    this.$store.dispatch("subscriptions/plans");
    this.$store.dispatch("user_subscription/get");
    this.$store.dispatch("user_subscription/getInvoices");
  },
  methods: {
    updateCard() {
      this.processing = true;
      this.createToken(this.updateCardForm).then(() => {
        if (!this.updateCardForm.error && this.form.token) {
          this.$store
            .dispatch("user_subscription/updateCard", this.form)
            .then(() => {
              this.coupon = null;
              this.processing = false;
            })
            .catch(() => {
              this.processing = false;
            });
        } else {
          this.processing = false;
        }
      });
    },
    createSubscription() {

      this.processing = true;

      if (this.userSubscription) {
        return this.updateSubscription();
      }

      this.createToken(this.createCardForm).then(() => {
        if (!this.createCardForm.error && this.form.token) {
          this.$store
            .dispatch("user_subscription/store", this.form)
            .then(() => {
              this.coupon = null;
              this.$store.dispatch("user/get");
            })
            .catch(() => {
              this.processing = false;
            });
        } else {
          this.processing = false;
        }
      });
    },
    updateSubscription() {
      this.$store
        .dispatch("user_subscription/patch", this.form)
        .then(() => {
          this.$store.dispatch("user_subscription/getInvoices");
          this.coupon = null;
          this.processing = false;
        })
        .catch(() => {
          this.processing = false;
        });
    },
    createToken(cardForm) {
      return new Promise(resolve => {
        if (!this.form.token) {
          cardForm.instance
            .createToken(cardForm.card)
            .then(result => {
              if (result.error) {
                cardForm.error = result.error.message;
              }
              this.form.token = result.token.id;
              resolve();
            })
            .catch(() => {
              resolve();
            });
        } else {
          resolve();
        }
      });
    },
    cancelSubscription() {
      this.$store.dispatch(
        "user_subscription/cancel",
        this.userSubscription.id
      );
    },
    downloadLink: function(invoice) {
      return "/subscription/invoices/" + invoice;
    }
  },
  computed: {
    invoices() {
      return this.$store.state.user_subscription.invoices;
    },
    isCanceled() {
      if (this.userSubscription) {
        return this.userSubscription.ends_at !== null;
      }
      return false;
    },
    currentCard() {
      if (this.userSubscriptionData) {
        return this.userSubscriptionData.card;
      }
    },
    userSubscription() {
      if (this.userSubscriptionData) {
        return this.userSubscriptionData.subscription;
      }
    },
    userSubscriptionData() {
      return this.$store.state.user_subscription.subscription;
    }
  }
};
</script>