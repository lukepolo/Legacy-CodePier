@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Your Profile</div>
                    <div class="panel-body">
                        <ul id="myTabs" class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#account-info" role="tab" data-toggle="tab">Account Info</a></li>
                            <li role="presentation" class=""><a href="#ssh-keys" role="tab" data-toggle="tab">SSH Keys</a></li>
                            <li role="presentation" class=""><a href="#server-providers" role="tab" data-toggle="tab">Server Providers</a></li>
                            <li role="presentation" class=""><a href="#repository-providers" role="tab" data-toggle="tab">Repository Providers</a></li>
                            <li role="presentation" class=""><a href="#notification-providers" role="tab" data-toggle="tab">Notification Providers</a></li>
                            <li role="presentation" class=""><a href="#subscription" role="tab" data-toggle="tab">Subscription</a></li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade active in" id="account-info">
                                <p>
                                    {!! Form::open(['action' => 'Auth\UserController@postMyProfile']) !!}
                                        <div class="form-group">
                                            {!! Form::label('name') !!}
                                            {!! Form::text('name', \Auth::user()->name, ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('email') !!}
                                            {!! Form::email('email', \Auth::user()->email, ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('new password') !!}
                                            {!! Form::password('new-password', ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('confirm password') !!}
                                            {!! Form::password('confirm-password', ['class' => 'form-control']) !!}
                                        </div>
                                        {!! Form::submit('Update Profile') !!}
                                    {!! Form::close() !!}
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="ssh-keys">
                                {!! Form::open(['action' => ['Auth\UserController@postAddSshKey', 1]]) !!}
                                <div class="form-group">
                                    {!! Form::label('name') !!}
                                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('Public Key') !!}
                                    {!! Form::textarea('ssh_key', null, ['class' => 'form-control']) !!}
                                </div>
                                {!! Form::submit('Install SSH Key') !!}
                                {!! Form::close() !!}
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Key Name</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach(\Auth::user()->sshKeys as $sshKey)
                                        <tr>
                                            <td>{{ $sshKey->name }}</td>
                                            <td><a href="{{ action('Auth\UserController@getRemoveSshKey', $sshKey->id) }}" class="fa fa-remove"></a></td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="server-providers">
                                <p>
                                    @foreach($serverProviders as $serverProvider)
                                        Integrate with {{ $serverProvider->name }} :

                                        @if(!\Auth::user()->userServerProviders->pluck('server_provider_id')->contains($serverProvider->id))
                                            <a href="{{ action('Auth\OauthController@newProvider', $serverProvider->provider_name) }}" class="btn btn-default">Integrate</a>
                                        @else
                                            <a href="{{ action('Auth\OauthController@getDisconnectService', [App\Models\UserServerProvider::class, \Auth::user()->userServerProviders->first(function($userServerProvider) use ($serverProvider) {
                                                return $userServerProvider->server_provider_id == $serverProvider->id;
                                            })->id]) }}">Disconnect</a>
                                        @endif
                                    @endforeach
                                </p>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="repository-providers">
                                @foreach($repositoryProviders as $repositoryProvider)
                                    <p>
                                        Integrate with {{ $repositoryProvider->name }} :
                                        @if(!\Auth::user()->userRepositoryProviders->pluck('repository_provider_id')->contains($repositoryProvider->id))
                                            <a href="{{ action('Auth\OauthController@newProvider', $repositoryProvider->provider_name) }}" class="btn btn-default">Integrate</a>
                                        @else
                                            <a href="{{ action('Auth\OauthController@getDisconnectService', [App\Models\UserRepositoryProvider::class, \Auth::user()->userRepositoryProviders->first(function($userRepositoryProvider) use ($repositoryProvider) {
                                                return $userRepositoryProvider->repository_provider_id == $repositoryProvider->id;
                                            })->id]) }}">Disconnect</a>
                                        @endif
                                    </p>
                                @endforeach
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="notification-providers">
                                @foreach($notificationProviders as $notificationProvider)
                                    <p>
                                        Integrate with {{ $notificationProvider->name }} :
                                        @if(!\Auth::user()->userNotificationProviders->pluck('notification_provider_id')->contains($notificationProvider->id))
                                            <a href="{{ action('Auth\OauthController@newProvider', $notificationProvider->provider_name) }}" class="btn btn-default">Integrate</a>
                                        @else
                                            <a href="{{ action('Auth\OauthController@getDisconnectService', [App\Models\UserNotificationProvider::class, \Auth::user()->userNotificationProviders->first(function($userNotificationProvider) use ($notificationProvider) {
                                                return $userNotificationProvider->notification_provider_id == $notificationProvider->id;
                                            })->id]) }}">Disconnect</a>
                                        @endif
                                    </p>
                                @endforeach

                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="subscription">

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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).on('click', '.new-card', function() {
            $('#card-info').toggleClass('hide');
        });
    </script>
@endpush
