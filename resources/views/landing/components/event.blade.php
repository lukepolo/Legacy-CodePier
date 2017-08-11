<div class="events--item delay" data-delay="{{ $event['delay'] }}">
    <div class="events--item-name">

        @component('landing.components.dropdown-event', ['title' => $event['title'], 'subEvents' => isset($subEvents)])

            @isset($subEvents)

                <ul>
                    <li>

                        @foreach($subEvents as $subEvent => $subSubEvents)

                            @component('landing.components.dropdown-event', ['title' => $subEvent, 'subEvents' => true])

                                @foreach($subSubEvents as $subEvent => $subEventName)
                                        @component('landing.components.dropdown-event', ['title' => $subEventName, 'subEvents' => false, 'hide' => true])
                                            <pre>{{ $subEvent }}</pre>
                                        @endcomponent
                                @endforeach
                                <br>
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

    <div class="events--item-time">
        15 seconds ago
    </div>
</div>