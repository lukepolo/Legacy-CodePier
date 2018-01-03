@if (session('status'))
    <div class="alert-success">
        <ul>
            <li>{{ session('status') }}</li>
        </ul>
    </div>
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