@if(!empty($currentDeployment = $site->lastDeployment))
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                {{ ucwords($currentDeployment->status) }} - <a href="#" target="_blank">{{
                    $currentDeployment->git_commit }}</a>
            </div>
            <div class="panel-body">
                @foreach($currentDeployment->events as $event)
                <div class="panel panel-default">
                    <div class="panel-body">
                        {{ $event->step->step }} -
                        @if($event->started && !$event->completed)
                        Running . . .
                        @else
                        @if($event->completed)
                        @if($event->failed)
                        FAILED
                        @else
                        Completed in {{ round($event->runtime, 2) }} seconds
                        @endif
                        <br>
                        @if(is_array($event->log))
                        @foreach($event->log as $logItem)
                        @if(!empty($logItem = trim(preg_replace('/codepier-done/', '', $logItem))))
                        {!! nl2br($logItem) !!}
                        @endif
                        @endforeach
                        @else
                        {!! str_replace('\n', '<br>', trim(nl2br($event->log), '"')) !!}
                        @endif
                        @else
                        Pending
                        @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div>
            @if(is_array($currentDeployment->log ))
            <pre>@foreach($currentDeployment->log as $logItem) @if(!empty($logItem = trim(preg_replace('/codepier-done/', '', $logItem)))){!! $logItem !!}<br>@endif @endforeach</pre>
            @endif
        </div>
    </div>
</div>
@endif


@foreach($site->deployments as $deployment)
<div class="panel-heading">
    {{ ucwords($deployment->status) }} - <a href="#" target="_blank">{{ $deployment->git_commit }}</a> <a href="#">(Revert)</a>
</div>
@endforeach