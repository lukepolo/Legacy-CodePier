<script>
  window.Laravel = <?php echo json_encode([
        'env' => config('app.env'),
        'csrfToken' => csrf_token(),
        'teams' => config('app.teams'),
        'version' => app()->make('gitCommit'),
        'stripeKey' => config('services.stripe.key'),
        'echoServerKey' => config('broadcasting.connections.pusher.key'),
        'serverTypes' => \App\Services\Systems\SystemService::SERVER_TYPES,
        'defaultNotificationTypes' => \App\Http\Controllers\EventController::DEFAULT_TYPES,
    ]); ?>
</script>