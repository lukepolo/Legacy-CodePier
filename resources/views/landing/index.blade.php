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
                <a href="/login" class="btn btn-primary btn-large">Get Started Now!</a>
            </div>
        </div>
    </section>

    <section id="section--intro" class="section">
        <div class="section--content">
            <h2>Your dock for DevOps</h2>
            <p>Look, just because you can, don't spend unnecessary time configuring and worrying about your deployments and processes. CodePier helps you manage it all so you have to time to do what you do best: building apps.</p>

            <br><br>

            <h2>A powerful event system help you monitor what's going on.</h2>
            <p>Visibility is key. CodePier's innovative events system makes sure you always have real time monitoring of your processes. Our events bar is a single location to track all of your deployments, updates, installs, workers, cron jobs and so much more. Expand events to view terminal output to easily de-bug any issues.</p>
        </div>

        <div class="section--img">
        </div>
    </section>

    <section id="section--features" class="section">
        <div class="section--content">
            <h2 class="text-center text-primary">Say something cool about features</h2>
            <h4 class="text-center">People love the fuck outta features</h4>

            <div class="features">
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Site priority design</h3>
                        <p>Server provisioning is customized based on your application's framework and requirements, alleviating you of time-consuming setup.
                        </p>
                    </div>
                </div>
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Select the server setup you need</h3>
                        <p>Whether you need a straight forward full stack server or multiple servers to handle your databases and workers, CodePier can configure them for you.
                        </p>
                    </div>
                </div>
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Advanced server configuration</h3>
                        <p>Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is advertised as the most popular gun in American crime. Do you believe that shit? It actually says that in the little book that comes with it: the most popular gun in American crime. Like they're actually proud of that shit.
                        </p>
                    </div>
                </div>
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Customize your deployment</h3>
                        <p>Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is advertised as the most popular gun in American crime. Do you believe that shit? It actually says that in the little book that comes with it: the most popular gun in American crime. Like they're actually proud of that shit.
                        </p>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <a href="" class="btn btn-primary">Start your free trial!</a>
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
                    <div data-video="205611637" class="video--controls-item active">Provision</div>
                    <div data-video="205614363" class="video--controls-item">Deploy</div>
                </div>
            </div>
        </div>
    </section>
    <section id="section--slider" class="section">
        <div class="section--content" >
            <div class="slider" id="features-slider">
                <div class="slider--item">
                    <div class="slider--img">
                        <img src="{{ asset('assets/img/CP_Logo_TX-onWhite.svg') }}">
                    </div>
                    <div class="slider--content">
                        <h5>Visibility</h5>
                        <h2>Server monitoring available at a glance.</h2>
                        <p>stuff</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="section--testimonials" class="section">
        <div class="section--content">
            <h2 class="text-center">Build, provision, deploy and manage your applications easily, all on your own servers.</h2>
            <div class="testimonials">
                <div class="slider" id="testimonials-slider">
                    <div class="testimonials--item">
                        <div class="testimonials--quote">
                            <p>quote</p>
                        </div>
                        <div class="testimonials--author">
                            {{--<div class="testimonials--img">--}}
                                {{--<img src="https://secure.gravatar.com/avatar/6e339067135ec0efc021d8137acc2e3a?s=400&d=mm&r=g">--}}
                            {{--</div>--}}
                            <div>
                                <div class="testimonials--name">
                                    Chris Johnson
                                </div>
                                <div class="testimonials--location">
                                    Indianapolis, IN
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="callout">
                    <p>See how other developers are using CodePier today. Get started with a 5-day free trial when you sign up.</p>
                    <button class="btn btn-primary">Sign Up Today</button>
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
