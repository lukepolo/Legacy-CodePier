@extends('layouts.public')

@section('content')
    <section id="cover-main" class="cover">
        <div class="cover--content">
            <div class="cover--logo-container">
                <img src="assets/img/codepier.svg">
            </div>
            <h1>You Build it. We Deploy it.</h1>
            <p>You're here to build apps. CodePier is here to help you manage your infrastructure, allow custom provisioning for each application, and eliminate downtime with zerotime deployments, plus, so much more. Come check it out.</p>
            <div class="btn-container">
                <a href="{{ route('login') }}" class="btn btn-primary btn-large">Join our Beta</a>
            </div>
        </div>
    </section>
    <section id="section-video" class="section">
        <div class="section--content">
            <div class="video">
                <h2 class="video--header">Deploying Your App</h2>
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
    <section id="comparison" class="section">
        <div class="section--content">
            <div class="table--responsive">
                <table class="table table--comparison table--light">
                    <tr>
                        <th class="row1"></th>
                        <th class="row2"><img src="/assets/img/codepier.svg" style="max-width: 130px;"></th>
                        <th class="row3">Forge</th>
                        <th class="row4">Heroku</th>
                        <th class="row5">Engine Yard</th>
                    </tr>
                    <tr>
                        <td>Languages</td>
                        <td>
                            GO, Java, Node.js, PHP, Python,Ruby <br>
                            <b>
                                <small>Durring beta we will only be supporting PHP</small>
                            </b>
                        </td>
                        <td>PHP</td>
                        <td>Ruby, Java, PHP, Python, Node.js, Scala, Clojure</td>
                        <td>Ruby, JRuby, PHP, Node.js</td>
                    </tr>
                    <tr>
                        <td>Automated Provisioning</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>Custom Server Features</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/kinda.svg">Installable plugins</td>
                        <td><img src="/assets/img/no.svg"></td>
                    </tr>
                    <tr>
                        <td>Docker Support</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>Databases</td>
                        <td>MariaDB, MySQL, MongoDB, PostgreSQL, SQLite</td>
                        <td>MariaDB, MySQL</td>
                        <td>PostgreSQL</td>
                        <td>MySQL, PostgreSQL</td>
                    </tr>
                    <tr>
                        <td>Horizontal Scaling</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>Vertical Scaling</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>Easy Custom Deployments</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                    </tr>
                    <tr>
                        <td>Zerotime Deployments out of box</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                    </tr>
                    <tr>
                        <td>Deploy Rollbacks</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>Cron Jobs</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/money.svg">Costs Extra</td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>Firewall Rules</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>Workers</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/money.svg">Costs Extra</td>
                        <td><img src="/assets/img/money.svg">Costs Extra</td>
                    </tr>
                    <tr>
                        <td>Free SSL Certificates</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                    </tr>
                    <tr>
                        <td>SSH Management</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>Custom Runnable Scripts</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                    </tr>
                    <tr>
                        <td>Allow Integration for Continuous Delivery</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>1 Click Application Installs</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/money.svg">Costs Extra</td>
                        <td><img src="/assets/img/no.svg"></td>
                    </tr>
                    <tr>
                        <td>Basic Server Monitoring</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>Platform independence</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/yes.svg">AWS Only</td>
                    </tr>
                    <tr>
                        <td>Team Management</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/money.svg">Costs Extra</td>
                        <td><img src="/assets/img/money.svg">Costs Extra</td>
                        <td><img src="/assets/img/yes.svg"></td>
                    </tr>
                    <tr>
                        <td>Easy Cost Calculation</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                    </tr>
                </table>
            </div>

            <div class="text-right">
                <p><em>* this is not a comprehensive list of features</em></p>
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
