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

            <p class="subheading">Build, Deploy & Mange your applications easily, all on your own servers.</p>

            <div class="cover--btns">
                <a href="/login" class="btn btn-primary btn-large">Start Now!</a>
            </div>
        </div>
    </section>

    <section id="section--intro" class="section">
        <div class="section--content">
            <h2>Your dock for DevOps</h2>
            <p>Look, just because I don't be givin' no man a foot massage don't make it right for Marsellus to throw Antwone into a glass motherfuckin' house, fuckin' up the way the nigger talks. Motherfucker do that shit to me, he better paralyze my ass, 'cause I'll kill the motherfucker, know what I'm sayin'?</p>

            <br><br>

            <h2>A powerful event system help you monitor what's going on.</h2>
            <p>Normally, both your asses would be dead as fucking fried chicken, but you happen to pull this shit while I'm in a transitional period so I don't wanna kill you, I wanna help you. But I can't give you this case, it don't belong to me. Besides, I've already been through too much shit this morning over this case to hand it over to your dumb ass.</p>
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
                        <h3>Feature Headline</h3>
                        <p>Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is advertised as the most popular gun in American crime. Do you believe that shit? It actually says that in the little book that comes with it: the most popular gun in American crime. Like they're actually proud of that shit.
                        </p>
                    </div>
                </div>
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Feature Headline</h3>
                        <p>Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is advertised as the most popular gun in American crime. Do you believe that shit? It actually says that in the little book that comes with it: the most popular gun in American crime. Like they're actually proud of that shit.
                        </p>
                    </div>
                </div>
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Feature Headline</h3>
                        <p>Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is advertised as the most popular gun in American crime. Do you believe that shit? It actually says that in the little book that comes with it: the most popular gun in American crime. Like they're actually proud of that shit.
                        </p>
                    </div>
                </div>
                <div class="features--item">
                    <div class="features--img">
                        <span class="icon-layers"></span>
                    </div>
                    <div class="features--content">
                        <h3>Feature Headline</h3>
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
    <section class="section">
        <div class="section--content">
            slider stuff
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://player.vimeo.com/api/player.js"></script>

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
