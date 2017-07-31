<div class="events--item hide delay" data-delay="{{ $event['delay'] }}">
    {{--'events--item-status-neutral' : event.status === 'Queued',--}}
    {{--'events--item-status-success' : event.status === 'Completed',--}}
    {{--'events--item-status-error' : event.status === 'Failed',--}}
    {{--'icon-spinner' : event.status === 'Running'--}}
    <div class="events--item-status"></div>
    <div class="events--item-name">

        @component('landing.components.dropdown-event', ['title' => $event['title'], 'subEvents' => isset($subEvents)])

            @isset($subEvents)

                <ul>
                    <li>

                        @foreach($subEvents as $subEvent => $subSubEvents)

                            @component('landing.components.dropdown-event', ['title' => $subEvent, 'subEvents' => true])

                                @foreach($subSubEvents as $subEventName => $subSubEvent)

                                    @if($subEventName === 0)
                                        {{ $subSubEvent }}
                                    @else
                                        @component('landing.components.dropdown-event', ['title' => $subEventName, 'subEvents' => false])
                                            <pre>{{ $subSubEvent }}</pre>
                                        @endcomponent
                                    @endif

                                @endforeach

                            @endcomponent

                        @endforeach

                    </li>
                </ul>

            @endisset

        @endcomponent
    </div>
    <div class="events--item-pile"><span class="icon-layers"></span> production</div>
    <div class="events--item-site">
        <span class="icon-browser"></span>
        my-app.io
    </div>

    {{--<div class="events--item-time">--}}
        {{--15 seconds ago--}}
    {{--</div>--}}
</div>