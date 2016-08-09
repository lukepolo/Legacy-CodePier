<template>
    <div>
        @if(\Auth::user()->subscribed())
            @if(\Auth::user()->subscription()->cancelled())
                Your subscription has been cancled and will end on {{ \Auth::user()->subscription()->ends_at }}
                @else
                Your next billing is on {{ \Auth::user()->subscription()->ends_at }}
            @endif
        @endif

        {!! Form::open(['action' => 'PaymentController@postSubscription']) !!}
        <?php setlocale(LC_MONETARY, 'en_US.UTF-8'); ?>
        @foreach($plans as $plan)
        <div class="radio">
            <label>
                @if(!\Auth::user()->subscribedToPlan($plan->id))
                {!! Form::radio('plan', $plan->id) !!}
                @else
                Current Plan -
                @endif
                {{ $plan->name }} ({{ money_format('%n', $plan->amount / 100) }} / {{ $plan->interval }}

                @if(!empty($plan->metadata->save))
                <b>
                    SAVE {{  money_format('%n', $plan->metadata->save) }} per {{ $plan->interval }}
                </b>
                @endif
                )
            </label>
        </div>
        @endforeach

        @if(\Auth::user()->hasCardOnFile())
        Use your {{ \Auth::user()->card_brand }} {{ \Auth::user()->card_last_four }}
        <div class="btn btn-link new-card">new card</div>
        @endif

        <div id="card-info" class="@if(\Auth::user()->hasCardOnFile()) hide @endif">
            Number
            {!! Form::text('number') !!}
            Exp Month
            {!! Form::text('exp_month') !!}
            Exp Year
            {!! Form::text('exp_year') !!}
            CVC
            {!! Form::password('cvc') !!}

        </div>

        {!! Form::submit('Subscribe') !!}
        {!! Form::close() !!}

        <a href="{{ action('PaymentController@getCancelSubscription') }}">Cancel Subscription</a>
    </div>

    @if(\Auth::user()->subscribed())
    <table>
        @foreach (\Auth::user()->invoices() as $invoice)
        <tr>
            <td>{{ $invoice->date()->toFormattedDateString() }}</td>
            <td>{{ $invoice->total() }}</td>
            <td><a href="{{ action('PaymentController@getUserInvoice', $invoice->id) }}">Download</a></td>
        </tr>
        @endforeach
    </table>
    @endif
    </div>
</template>


<script>
    export default {
        data() {
            return {
                ssh_keys : []
            }
        },
        methods : {
            onSubmit: function() {
                Vue.http.post(laroute.action('User\Features\UserSshKeyController@store'), this.getFormData(this.$el)).then((response) => {
                    this.ssh_keys.push(response.json());
                }, (errors) => {
                    alert(error);
                });
            }
        },
        mounted () {
//            Vue.http.get(laroute.action('User\Features\UserSshKeyController@index')).then((response) => {
//                this.ssh_keys = response.json();
//            }, (errors) => {
//                alert(error);
//            })
        },
    }
</script>