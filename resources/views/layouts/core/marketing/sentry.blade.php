<script src="https://cdn.ravenjs.com/3.15.0/raven.min.js"></script>
<script>
  Raven.config('{{ config('sentry.js_dsn') }}').install()
</script>