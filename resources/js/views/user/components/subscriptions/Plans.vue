<template>
    <div class="pricing--item" :class="{ selected : isActive }">
        <div class="pricing--header">
            <div class="pricing--header-name">{{ title }}</div>
        </div>
        <div class="pricing--features">
            <template v-if="type !== 'captain'">
                <div class="flyform--group-radio" v-for="plan in plans">
                    <label>
                        <input v-model="form.plan" type="radio" name="plan" :value="plan.plan_id">
                        <span class="icon"></span>
                        ${{ (plan.amount/ 100) }} / {{ plan.interval }}
                        <strong v-if="plan.metadata.save">
                            SAVE ${{ plan.metadata.save }}.00 per {{ plan.interval }}
                        </strong>
                        <template v-if="userSubscription && userSubscription.active_plan === plan.plan_id">
                            <h4 class="text-success" style="display: inline-flex;">&nbsp; (Selected)</h4>
                            <small>
                                <template v-if="isCanceled || userSubscription.active_plan !== userSubscription.stripe_plan">
                                    Valid Until
                                </template>
                                <template v-else>
                                    Next billing date
                                </template>
                                : {{ parseDate(userSubscriptionData.subscriptionEnds.date).format('l') }}
                            </small>
                        </template>
                    </label>
                </div>
            </template>
            <template v-else>
                <div class="pricing--coming">COMING SOON!</div>
            </template>


            <template v-if="type === 'captain'">
                <hr>
                <ul>
                    <li><strong>Priority Support</strong></li>
                    <li><strong>Unlimited</strong> Sites</li>
                    <li><strong>Unlimited</strong> Servers</li>
                    <li><strong>Unlimited</strong> Deployments</li>
                    <li>Multiple Server Types</li>
                    <li>Buoys (1 Click Apps)</li>
                    <li>Server Monitoring</li>
                    <li>Teams</li>
                    <li>API Access</li>

                </ul>
            </template>
            <template v-else-if="type === 'firstmate'">
                <hr>
                <ul>
                    <li><strong>Unlimited</strong> Sites</li>
                    <li>30 Servers</li>
                    <li><strong>Unlimited</strong> Deployments</li>
                    <li>Multiple Server Types</li>
                    <li>Buoys (1 Click Apps)</li>
                    <li>Server Monitoring</li>
                </ul>
            </template>
        </div>
    </div>
</template>

<script>
export default {
  props: ["selectedPlan", "title", "type"],
  data() {
    return {
      form: {
        plan: this.selectedPlan,
      },
    };
  },
  watch: {
    selectedPlan: function() {
      this.$set(this.form, "plan", this.selectedPlan);
    },
    "form.plan": function() {
      this.$emit("update:selectedPlan", this.form.plan);
    },
  },
  computed: {
    isActive() {
      if (this.userSubscription) {
        return this.plans.find(
          (plan) => plan.id === this.userSubscription.active_plan,
        );
      }
    },
    plans() {
      return this.$store.state.subscriptions.plans.filter((plan) => {
        return plan.plan_id.indexOf(this.type) > -1;
      });
    },
    isCanceled() {
      if (this.userSubscription) {
        return this.userSubscription.ends_at !== null;
      }
      return false;
    },
    userSubscription() {
      if (this.userSubscriptionData) {
        return this.userSubscriptionData.subscription;
      }
    },
    userSubscriptionData() {
      return this.$store.state.user.subscription.subscription;
    },
  },
};
</script>
