@if (session('status'))
    <div class="text-error">
    <div class="text-center">
        <ul style="list-style: none; padding: 0">
            {{ session('status') }}
        </ul>
    </div>
    </p>
@endif

@if (count($errors) > 0)
    <div class="alert-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif