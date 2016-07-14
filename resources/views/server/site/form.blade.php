<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                Add a site
            </div>
            <div class="panel-body">
                {!! Form::open(['action' => ['SiteController@postCreateSite', $server->id]]) !!}
                    Domain
                    {!! Form::text('domain') !!}
                    WildCard Domain
                    {!! Form::hidden('wildcard_domain', false) !!}
                    {!! Form::checkbox('wildcard_domain', true) !!}
                    {!! Form::submit('Create Site') !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>