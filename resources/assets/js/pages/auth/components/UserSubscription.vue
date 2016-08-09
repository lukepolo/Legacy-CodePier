<template>
    <div>
        <template v-if="validSubscription">
            \Auth::user()->subscription()->cancelled()
            Your subscription has been cancled and will end on \Auth::user()->subscription()->ends_at
            Your next billing is on \Auth::user()->subscription()->ends_at
        </template>

        <form v-on:submit.prevent="onSubmit">
            <div class="radio" v-for="plan in plans">
                <label>
                    <template v-if="subscribedToPlan(plan.id)">
                        Current Plan -
                    </template>
                    <template v-else>
                        <input type="radio" name="plan" :value="plan.id">
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
                <div class="btn btn-link new-card">new card</div>
            </template>

            <div id="card-info" :class="{hide : user.card_brand}">
                <label>Number</label>
                <input type="text" name="number">

                <label>Exp Month</label>
                <input type="text" name="exp_month">

                <label>Exp Year</label>
                <input type="text" name="exp_year">

                <label>CVC</label>
                <input type="password" name="cvc">
            </div>

            <button type="submit">Save Subscription</button>
        </form>

        <a href="#" v-if="validSubscription">Cancel Subscription</a>

        <template v-if="invoices">
            <table>
                <tr v-for="invoice in invoices">
                    <td> {{ invoice.date }}</td>
                    <td> {{ invoice.total }}</td>
                    <td><a href="#">Download</a></td>
                </tr>
            </table>
        </template>
    </div>
</template>


<script>
    export default {
        props: ["user"],
        data() {
            return {
                plans: [],
                subscription: null,
                invoices : []
            }
        },
        computed : {
            validSubscription : function() {
                return this.subscription != null;
            }
        },
        methods: {
            subscribedToPlan: function (plan_id) {
                if (this.validSubscription && this.subscription.plan_id == plan_id) {
                    return true;
                }
                return false;
            },
            onSubmit: function () {
                Vue.http.post(laroute.action('User\Subscription\UserSubscriptionController@store'), this.getFormData(this.$el)).then((response) => {
                    this.ssh_keys.push(response.json());
                }, (errors) => {
                    alert(error);
                });
            }
        },
        mounted () {
            Vue.http.get(laroute.action('SubscriptionController@index')).then((response) => {
                this.plans = response.json();
            }, (errors) => {
                alert(error);
            })
        },
    }
</script>