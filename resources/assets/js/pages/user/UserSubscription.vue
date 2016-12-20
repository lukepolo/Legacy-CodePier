<template>
    <section>
        <template v-if="validSubscription">
            <p v-if="isCanceled">
                Your subscription has been canceled and will end on {{ user_subscription.ends_at }}
            </p>
            <p v-else>
                <template v-if="upcomingSubscription">
                    Your next billing is on {{ upcomingSubscription.date }}
                </template>
            </p>
        </template>

        <div class="jcf-form-wrap">
            <form @submit.prevent="createSubscription" class="floating-labels">

                <div class="jcf-input-group input-radio">
                    <div class="input-question">Select your plan</div>
                    <label v-for="plan in plans">
                        <input v-model="form.plan" type="radio" name="plan" :value="plan.id">
                        <span class="icon"></span>

                        <p>
                            {{ plan.name }} ({{ plan.amount }} / {{ plan.interval }}
                        </p>

                        <b v-if="plan.metadata.save">
                            SAVE {{ plan.metadata.save }} per {{ plan.interval }}
                        </b>
                    </label>
                </div>


                <template v-if="user.card_brand">
                    Use your {{ user.card_brand }} {{ user.card_last_four }}
                    <div @click="showCardForm = !showCardForm" class="btn btn-link new-card">new card</div>
                </template>

                <div id="card-info" :class="{hide : !showCardForm}">

                    <div class="jcf-input-group">
                        <input v-model="form.number" type="text" name="number">
                        <label for="number">
                            <span class="float-label">Number</span>
                        </label>
                    </div>

                    <div class="jcf-input-group">
                        <input v-model="form.exp_month" type="text" name="exp_month">
                        <label for="exp_month">
                            <span class="float-label">Exp Month</span>
                        </label>
                    </div>

                    <div class="jcf-input-group">
                        <input v-model="form.exp_year" type="text" name="exp_year">
                        <label for="exp_year">
                            <span class="float-label">Exp Year</span>
                        </label>
                    </div>

                    <div class="jcf-input-group">
                        <input v-model="form.cvc" type="password" name="cvc">
                        <label for="cvc">
                            <span class="float-label">CVC</span>
                        </label>
                    </div>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Save Subscription</button>
                </div>
            </form>
        </div>

        <a @click="cancelSubscription" v-if="validSubscription && !isCanceled">Cancel Subscription</a>

        <template v-if="invoices.length">
            <table>
                <tr v-for="invoice in invoices">
                    <td> {{ invoice.date }}</td>
                    <td> {{ invoice.total }}</td>
                    <td><a :href="downloadLink(invoice.id)">Download</a></td>
                </tr>
            </table>
        </template>
    </section>
</template>
<script>
    export default {
        data() {
            return {
                showCardForm: user.card_brand ? false : true,
                form: {
                    cvc: 123,
                    plan: null,
                    number: "4242424242424242",
                    exp_year: 2022,
                    exp_month: 11
                }
            }
        },
        created() {
            this.fetchData();
        },
        computed: {
            user() {
                return this.$store.state.userStore.user;
            },
            plans() {
                let plans = this.$store.state.userSubscriptionsStore.plans;

                if(this.validSubscription) {
                    let plan = _.find(plans, { id : this.user_subscription.stripe_plan});
                    if(plan) {
                        this.form.plan = plan.id
                    }
                }

                return plans;
            },
            invoices() {
                return this.$store.state.userSubscriptionsStore.user_invoices;
            },
            user_subscription() {
                return this.$store.state.userSubscriptionsStore.user_subscription;
            },
            validSubscription() {
                return this.$store.state.userSubscriptionsStore.valid_subscription;
            },
            upcomingSubscription() {
                return this.$store.state.userSubscriptionsStore.user_upcoming_subscription;
            },
            isCanceled() {
                return this.user_subscription.ends_at != null;
            }
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getPlans');
                this.$store.dispatch('getUserInvoices');
                this.$store.dispatch('getUserSubscription');
                this.$store.dispatch('getUpcomingSubscription');
            },
            createSubscription() {
                this.$store.dispatch('createUserSubscription', this.form).then(() => {
                    this.form = this.$options.data().form;
                });
            },
            cancelSubscription() {
                this.$store.dispatch('cancelSubscription', this.user_subscription.id);
            },
            downloadLink: function (invoice_id) {
                return this.action('User\Subscription\UserSubscriptionInvoiceController@show', {invoice: invoice_id});
            }
        }
    }
</script>