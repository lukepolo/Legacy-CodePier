@extends('layouts.public')

@section('content')
    <section id="section--hero" class="cover">
        <div class="cover--content">
            <div class="cover--logo">
                <img src="{{ asset('assets/img/CP_Logo_TX-onWhite.svg') }}">
            </div>
            <h1>Your dock for D<span class="small-caps">ev</span>Ops</h1>

            <p class="subheading">Build, Deploy & Mange your applications easily, all on your own servers.</p>

            <div class="cover--btns">
                <a href="/login" class="btn btn-primary btn-large">Join our Beta</a>
            </div>
        </div>
    </section>

    <section id="section--video" class="section">
        <div class="section--content">
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
@endsection

@push('scripts')
    <script src="https://player.vimeo.com/api/player.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g=" crossorigin="anonymous"></script>

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
