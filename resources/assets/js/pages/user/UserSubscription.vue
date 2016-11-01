<template>
    <section>
        <template v-if="validSubscription">
            <p v-if="isCanceled">
                Your subscription has been canceled and will end on {{ user_subscription.ends_at }}
            </p>
            <p v-else>
                <template v-if="!_.isEmpty(upcomingSubscription)">
                    Your next billing is on {{ upcomingSubscription.date }}
                </template>
            </p>
        </template>

        <form @submit.prevent="createSubscription">
            <div class="radio" v-for="plan in plans">
                <label>
                    <template v-if="subscribedToPlan(plan.id)">
                        Current Plan -
                    </template>
                    <template v-else>
                        <input v-model="form.plan" type="radio" name="plan" :value="plan.id">
                    </template>

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
                <label>Number</label>
                <input v-model="form.number" type="text" name="number">

                <label>Exp Month</label>
                <input v-model="form.exp_month" type="text" name="exp_month">

                <label>Exp Year</label>
                <input v-model="form.exp_year" type="text" name="exp_year">

                <label>CVC</label>
                <input v-model="form.cvc" type="password" name="cvc">
            </div>

            <button type="submit">Save Subscription</button>
        </form>

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
    import UserNav from './components/UserNav.vue';
    import LeftNav from './../../core/LeftNav.vue';
    export default {
        components: {
            LeftNav,
            UserNav
        },
        data() {
            return {
                plans: [],
                showCardForm: user.card_brand ? false : true,
                form: {
                    cvc: null,
                    plan: null,
                    number: null,
                    exp_year: null,
                    exp_month: null
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
                return this.$store.state.userSubscriptionsStore.plans;
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
            },
            subscribedToPlan: function (plan) {
                if (this.validSubscription && this.user_subscription.stripe_plan == plan) {
                    return true;
                }
                return false;
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