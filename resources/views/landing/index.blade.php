@extends('layouts.public')

@section('content')
    <section id="section--hero" class="cover">
        <div class="cover--img">
            <img src="{{ asset('/assets//img/Boat_Dock.svg') }}">
        </div>
        <div class="cover--content">
            {{--<div class="cover--logo">--}}
                {{--<img src="{{ asset('assets/img/CP_Logo_TX-onWhite.svg') }}">--}}
            {{--</div>--}}
            <h3>You build it,</h3>
            <h1>We Deploy It.</h1>

            <p class="subheading">Build, provision, deploy and manage your applications easily, all on your own servers.</p>

            <div class="cover--btns">
                <a href="{{ action('Auth\LoginController@login') }}?showRegisterForm=true" class="btn btn-primary btn-large">Get Started Now!</a>
            </div>
        </div>
    </section>

    <section id="section--intro" class="section">
        <div class="section--content">
            <h2>Your dock for DevOps</h2>
            <p>Look, just because you can, it doesn't mean you have to spend unnecessary time configuring and worrying about your deployments and processes. CodePier helps you manage it all so you have time to do what you do best: building apps.</p>

            <br><br>

            <h2>A powerful events system helps you monitor what's going on.</h2>
            <p>Visibility is key. CodePier's innovative events system makes sure you always have real time monitoring of your processes. Our events bar provides a single location to track all of your deployments, updates, installs, workers, cron jobs and so much more. Get immediate feedback to manage and debug your app.</p>
        </div>

        <div class="section--img">
            <img src="{{ asset('assets/img/deploy.gif')}}">
        </div>
    </section>

    <section id="section--features" class="section">
        <div class="section--content">
            <h2 class="text-center text-primary">Everything you need to manage complex deployments</h2>
            <h4 class="text-center">Designed around the way you work</h4>

            <div class="features">
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Site priority design</h3>
                        <p>Server provisioning is customized based on your application's framework and requirements, alleviating you of time-consuming setup and boosting your productivity.
                        </p>
                    </div>
                </div>
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Advanced server configuration</h3>
                        <p>Sometimes you need a bit more control. So you'd like to use MariaDB instead of MySQL? No problem. Customize all of your database, firewall, monitoring, node features and more.</p>
                    </div>
                </div>
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Customize your deployment</h3>
                        <p>CodePier initiates steps for your deployment, but allows for the flexibility you need. Reorder, remove, or add addional scripts to your deployment process to ensure the exact configuration you need for each application.</p>
                    </div>
                </div>
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Zero downtime deployments</h3>
                        <p>Deploy as often as you need and your users will never experience interruptions. Problem with your deployment? No worries! You can easily rollback to a working version.</p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="{{ action('Auth\LoginController@login') }}?showRegisterForm=true" class="btn btn-primary">Start your free trial!</a>
            </div>
        </div>
    </section>


    <section id="section--video" class="section">
        <div class="section--content">
            <h2 class="text-center">Deploying your app is easy!</h2>
            <div class="video">
                <div class="video--item">
                    <div class="video--item-embed">
                        <div id='deploy_app_url'></div>
                    </div>
                </div>
                <div class="video--controls">
                    <div data-video="253128478" class="video--controls-item active">Build</div>
                    <div data-video="253128360" class="video--controls-item">Provision</div>
                    <div data-video="253128436" class="video--controls-item">Deploy</div>
                </div>
            </div>
        </div>
    </section>
    <section id="section--slider" class="section">
        <div class="section--content" >
            <div class="slider" id="features-slider">
                <div class="slider--item">
                    <div class="slider--img">
                        <img src="{{ asset('assets/img/serverStats.png') }}">
                    </div>
                    <div class="slider--content">
                        <h5>Visibility</h5>
                        <h2>Server monitoring available at a glance.</h2>
                        <p>Stay on top of your server health. CodePier monitors data usage, memory and CPU load. Easy visibility helps you mitigate a problem before it happens. We've got your back! CodePier will even notify you via email if you've missed something.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="section--testimonials" class="section">
        <div class="section--content">
            <h2 class="text-center">Build, provision, deploy and manage your applications easily, all on your own servers.</h2>
            <div class="testimonials" style="grid-template-columns: 1fr; text-align: center;">
                {{--<div class="slider" id="testimonials-slider">--}}
                    {{--<div class="testimonials--item">--}}
                        {{--<div class="testimonials--quote">--}}
                            {{--<p>quote</p>--}}
                        {{--</div>--}}
                        {{--<div class="testimonials--author">--}}
                            {{--<div class="testimonials--img">--}}
                                {{--<img src="https://secure.gravatar.com/avatar/6e339067135ec0efc021d8137acc2e3a?s=400&d=mm&r=g">--}}
                            {{--</div>--}}
                            {{--<div>--}}
                                {{--<div class="testimonials--name">--}}
                                    {{--Chris Johnson--}}
                                {{--</div>--}}
                                {{--<div class="testimonials--location">--}}
                                    {{--Indianapolis, IN--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}

                <div class="callout">
                    <p>See how other developers are using CodePier today. Get started with a 5-day free trial when you sign up.</p>
                    <a class="btn btn-primary" href="{{ action('Auth\LoginController@login') }}?showRegisterForm=true"  >Sign Up Today</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://player.vimeo.com/api/player.js"></script>
    <script>
        $(document).ready(function(){
            $('#features-slider').slick({
                arrows: false,
                //dots: true,
                autoplay: true,
                autoplaySpeed: 8000,
            });

            $('#testimonials-slider').slick({
                arrows: false,
                dots: true,
                autoplay: true,
                autoplaySpeed: 8000,
            });
        });
    </script>

    <script>
        var player;

        switchVideo($('.video--controls-item').first().data('video'))

        $(document).on('click', '.video--controls-item', function () {
            $('.video--controls-item').removeClass('active')
            $(this).addClass('active')
            switchVideo($(this).data('video'))
        })

        function switchVideo(video) {
            if(!player) {
                player = new Vimeo.Player('deploy_app_url', { id : video })
                player.on('ended', function() {
                    var nextVideo = $('.video--controls-item.active').next()
                    if(nextVideo) {
                        $('.video--controls-item').removeClass('active')
                        nextVideo.addClass('active')
                        switchVideo(nextVideo.data('video'))
                    }
                });
            } else {
                player.loadVideo(video).then(function() {
                    player.play()
                })
            }
        }
    </script>
@endpush
