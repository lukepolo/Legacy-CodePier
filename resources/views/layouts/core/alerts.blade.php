@if (!empty(\Session::get('success')))
    <div class="container">
        <div class="col-md-6 col-md-offset-3 alert alert-success">
            {{ \Session::get('success') }}
        </div>
    </div>
@endif