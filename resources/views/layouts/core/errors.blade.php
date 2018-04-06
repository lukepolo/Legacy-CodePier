@if($errors->count())
    <script>
      app.showError('{{ $errors->first() }}')
    </script>
@endif
@if (session('success'))
    <script>
      app.showSuccess('{{ session('success') }}')
    </script>
@endif