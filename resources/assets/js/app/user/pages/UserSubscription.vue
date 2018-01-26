<template>
    <section>
        <template v-if="userSubscription">
            <div class="alert alert-warning" v-if="userSubscriptionData.isOnTrail">
                Your free trial ends on <strong>{{ parseDate(userSubscription.trial_ends_at).format('l') }}</strong>
            </div>

            <div class="alert alert-error" v-if="isCanceled">
                Your subscription has been canceled and will end on {{ parseDate(userSubscription.ends_at).format('l') }}
            </div>
        </template>

        <div class="text-center" v-if="!userSubscription">
            <h2>Start your 5 day free trial!</h2>
        </div>

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
                    <hr>
                    <ul>
                        <li>1 Site</li>
                        <li>1 Full Stack Server</li>
                        <li><strong>Unlimited</strong> Deployments</li>
                    </ul>
                </div>
            </div>
            <plans :selectedPlan.sync="form.plan" title="First Mate" type="firstmate"></plans>
            <plans :selectedPlan.sync="form.plan" title="Captain" type="captain"></plans>
        </div>

        <form @submit.prevent="createSubscription" method="post">
            <br><br>

            <div class="grid-2">
                <div class="grid--item">
                    <card v-if="!userSubscription" cardType="createCardForm" :card.sync="createCardForm.card" :error.sync="createCardForm.error" :instance.sync="createCardForm.instance"></card>

                    <form @submit.prevent="updateCard" method="post" v-if="currentCard">
                        <div class="flyform--group">
                            <div class="flyform--input-icon-right" v-if="!showCreditForm">
                                <a @click="showCreditForm = true"><span class="icon-pencil"></span> </a>
                            </div>
                            <input type="text" :value="`${currentCard.brand} ending in ${currentCard.last4}`" disabled v-if="!showCreditForm">
                            <label>Payment Method</label>

                            <div v-if="showCreditForm" class="stripeContainer">
                                <card cardType="updateCardForm" :card.sync="updateCardForm.card" :error.sync="updateCardForm.error" :instance.sync="updateCardForm.instance"></card>
                            </div>

                        </div>

                        <div class="flyform--footer-btns" v-if="showCreditForm">
                            <span class="btn btn-small" @click="showCreditForm = false">Cancel</span>
                            <button class="btn btn-primary btn-small" :class="{ 'btn-disabled' : processing }">Update Card</button>
                        </div>
                    </form>

                </div>
                <div class="grid--item">
                    <div class="flyform--group coupon-form">
                        <input type="text" name="coupon" v-model="form.coupon" placeholder=" ">
                        <label for="coupon">Coupon Code</label>
                    </div>

                    <div class="alert alert-success" v-if="isSubscribed && hasCoupon">
                        <strong>{{ hasCoupon.coupon.id }} -</strong> {{ hasCoupon.coupon.percent_off }}% off {{ hasCoupon.coupon.duration }}
                    </div>
                </div>

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

                <div class="flyform--footer-links" v-if="userSubscription">
                    <button class="text-error" @click="cancelSubscription" v-if="!isCanceled">
                        Cancel Subscription
                    </button>
                </div>
            </div>
        </form>

        <template v-if="invoices.length">
            <br><br>
            <h3>Payment History</h3>
            <table>
                <tr v-for="invoice in invoices">
                    <td> {{ parseDate(invoice.date.date).format('l') }}</td>
                    <td> ${{ invoiceTotal(invoice.total) }}</td>
                    <td class="text-right"><a :href="downloadLink(invoice.id)">Download</a></td>
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
        coupon: null,
        subscription: null
      }),
      createCardForm: {
        card: null,
        error: null,
        instance: null
      },
      updateCardForm: this.createForm({
        card: null,
        error: null,
        instance: null
      })
    };
  },
  watch: {
    userSubscription: function() {
      if (this.userSubscription) {
        this.form.plan = this.userSubscription.active_plan;
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
              this.updateCardForm.reset();
              this.showCreditForm = false;
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
              this.processing = false;
              this.$store.dispatch("user/get");
              this.$store.dispatch("user_subscription/getInvoices");
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
      if (this.form.plan !== "cancel") {
        this.processing = true;

        return this.$store
          .dispatch("user_subscription/patch", this.form)
          .then(() => {
            this.$store.dispatch("user_subscription/getInvoices");
            this.coupon = null;
            this.processing = false;
          })
          .catch(() => {
            this.processing = false;
          });
      }

      this.cancelSubscription();
    },
    createToken(cardForm) {
      return new Promise(resolve => {
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
      });
    },
    cancelSubscription() {
      this.processing = true;

      this.$store
        .dispatch("user_subscription/cancel", this.userSubscription.id)
        .then(() => {
          this.processing = false;
          this.$store.dispatch("user_subscription/getInvoices");
        });
    },
    downloadLink: function(invoice) {
      return "/subscription/invoices/" + invoice;
    },
    invoiceTotal(total) {
      return (total / 100).toFixed(2);
    }
  },
  computed: {
    hasCoupon() {
      if (
        this.userSubscriptionData &&
        this.userSubscriptionData.subscriptionDiscount
      ) {
        return this.userSubscriptionData.subscriptionDiscount;
      }
      return false;
    },
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
