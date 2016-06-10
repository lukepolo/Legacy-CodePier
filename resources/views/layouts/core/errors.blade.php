@if (count($errors) > 0)
    <div class="container">
        <div class="col-md-6 col-md-offset-3 alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif