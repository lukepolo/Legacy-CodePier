@if (session('status'))
    <p class="text-error">
    <div class="text-center">
        <ul style="list-style: none; padding: 0">
            {{ session('status') }}
        </ul>
    </div>
    </p>
@endif

@if (count($errors) > 0)
    <p class="text-error">
    <div class="text-center">
        <ul style="list-style: none; padding: 0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <br>
    </p>
@endif