<section class="section">
    <footer class="events" style="height:600px;">
        <div class="header events--header">
            <h4>
                <a class="toggle">
                    <span class="icon-warning"></span> Events
                </a>
            </h4>
        </div>
        <div class="events--collapse" id="collapseEvents">
            <ul class="filter">
                <li class="filter--label">
                    <span>Event Filters</span>
                </li>
                <li class="filter--item dropdown" ref="piles">
                    <a href="#" role="button" class="dropdown-toggle">
                        <strong>Pile:</strong>
                        <span class="filter--item-selection">
                        production
                    </span>
                        <span class="icon-arrow-up"></span>
                    </a>
                </li>

                <li class="filter--item dropdown" ref="sites">
                    <a href="#" role="button" class="dropdown-toggle">
                        <strong>Site:</strong> <span class="filter--item-selection">my-app.io</span> <span class="icon-arrow-up"></span>
                    </a>
                </li>

                <li class="filter--item dropdown" ref="servers">
                    <a href="#" role="button" class="dropdown-toggle">
                        <strong>Server:</strong> <span class="filter--item-selection">all</span> <span class="icon-arrow-up"></span>
                    </a>
                </li>

                <li class="filter--item dropdown" ref="types">
                    <a href="#" role="button" class="dropdown-toggle">
                        <strong>Event Type:</strong> <span class="filter--item-selection">all</span> <span class="icon-arrow-up"></span>
                    </a>
                </li>
            </ul>

            <div class="events--container">
                @include('landing.components.event', [
                    'event' => [
                        'delay'=> 2000,
                        'title' => 'Launch App'
                    ]
                ])

                @include('landing.components.event', [
                    'event' => [
                        'delay' =>  2000,
                        'title' => 'Deploy my-app.io'
                    ],
                    'subEvents' => [
                        'Web Server 1' => [
                            'Setup Zero Time Deployment',
                            'Clone Repository' => 'Cloning to server',
                        ],
                        'Web Server 2' => [
                            'Setup Zero Time Deployment',
                            'Clone Repository' => 'Cloning to server',
                        ],
                        'Database Server' => [
                            'Run Migrations' => 'Ran 15 migrations',
                        ],
                        'Worker Server' => [
                            'Setup Zero Time Deployment',
                            'Clone Repository' => 'Cloning to server',
                            'Restart Workers' => 'Restarted 50 workers',
                        ]

                    ]
                ])

                @include('landing.components.event', [
                     'event' => [
                         'delay'=> 2000,
                         'title' => 'Provisioned Worker Server'
                     ]
                 ])

                @include('landing.components.event', [
                    'event' => [
                        'delay'=> 2000,
                        'title' => 'Provisioned Database Server '
                    ]
                ])

                @include('landing.components.event', [
                    'event' => [
                        'delay'=> 2000,
                        'title' => 'Install Let\'s Encrypt SSL Certificate for my-app.io'
                    ]
                ])

                @include('landing.components.event', [
                    'event' => [
                        'delay'=> 2000,
                        'title' => 'Provisioned Web Server 2'
                    ]
                ])

                @include('landing.components.event', [
                    'event' => [
                        'delay'=> 2000,
                        'title' => 'Provisioned Web Server 1'
                    ]
                ])

                @include('landing.components.event', [
                    'event' => [
                        'delay'=> 0,
                        'title' => 'Provisioned Load Balancer'
                    ]
                ])

                <div id="events-loading" style="text-align: center;padding: 30px" class="delay" data-delay="4000">
                    <span class="icon-spinner"></span>
                </div>
            </div>
        </div>
    </footer>
</section>

@push('scripts')
    <script>
        var delay = 0;
        $(document).ready(function() {
            loop()
        })

        function loop() {
            $($('.delay').get().reverse()).each(function() {

                delay = delay + $(this).data('delay') / 2
                var element = $(this)

                window.setTimeout(function() {
                    element.toggleClass('hide')
                }, delay)
            })

            window.setTimeout(function() {
                delay = 0
                $('.delay').toggleClass('hide')
                loop()
            }, delay + 5000)
        }
    </script>
@endpush