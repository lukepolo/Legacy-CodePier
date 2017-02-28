@extends('layouts.public')

@section('content')
    <section id="cover-main" class="cover">
        <div class="cover--content">
            <div class="cover--logo-container">
                <img src="assets/img/codepier.svg">
            </div>
            <h1>You Build it. We Deploy it.</h1>
            <p>You're here to build apps. CodePier is here to help you manage your infrastructure, allow custom provisioning for each application, and eliminate downtime with zerotime deployments, plus, so much more. Come check it out.</p>

            @if(!(\Illuminate\Support\Facades\Session::get('registered_for_beta') || \Illuminate\Support\Facades\Cookie::get('registered_for_beta')))
                <form method="POST" action="{{ action('PublicController@subscribe') }}">
                    {{ csrf_field() }}
                    <div class="jcf-form-wrap">
                        <div class="jcf-input-group">
                            <input type="email" id="email" name="email" required value="{{ old('email') }}">
                            <label for="email"><span class="float-label">Enter your email to join our beta</span></label>
                        </div>
                        @if($errors->count())
                            <span class="error">{{ $errors->first() }}</span>
                        @endif
                    </div>

                    <div class="btn-container">
                        <button class="btn btn-primary btn-large">Join our Beta</button>
                    </div>
                </form>

            @else
                <hr>
                <h3>Thanks for registering for the beta! We will email you when your invite is ready!</h3>
            @endif
        </div>
    </section>
    <section id="section-video" class="section">
        <div class="section--content">
            <div class="video">
                <div class="video--item">
                    <div class="video--item-embed">
                        <iframe src='https://player.vimeo.com/video/205614363' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="comparison" class="section">
        <div class="section--content">
            <div class="table--responsive">
                <table class="table table--comparison">
                    <tr>
                        <th class="row1"></th>
                        <th class="row2"><img src="/assets/img/codepier.svg" style="max-width: 130px;"></th>
                        <th class="row3">Forge</th>
                        <th class="row4">Heroku</th>
                        <th class="row5">Engine Yard</th>
                    </tr>
                    <tr>
                        <td>Languages</td>
                        <td>Ruby, Java, PHP, Python, Node.js</td>
                        <td>PHP</td>
                        <td>Ruby, Java, PHP, Python, Node.js, Scala, Clojure</td>
                        <td>Ruby, JRuby, PHP, Node.js</td>
                    </tr>
                    <tr>
                        <td>Multiple Frameworks Support</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
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
                        <td><img src="/assets/img/no.svg"> * Installable plugins</td>
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
                        <td>MariaDB, Mysql, MongodB, PostgreSQL, SqlLite</td>
                        <td>MariaDB, Mysql</td>
                        <td>PostgreSQL</td>
                        <td>Mysql, PostgreSQL</td>
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
                        <td><img src="/assets/img/no.svg"> * Cost Extra</td>
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
                        <td><img src="/assets/img/no.svg">* Cost Extra</td>
                        <td><img src="/assets/img/no.svg">* Cost Extra</td>
                    </tr>
                    <tr>
                        <td>Free SSL Certificates</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
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
                        <td><img src="/assets/img/no.svg"></td>
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
                        <td><img src="/assets/img/no.svg">* AWS Only</td>
                        <td><img src="/assets/img/yes.svg">* AWS Only</td>
                    </tr>
                    <tr>
                        <td>Team Management</td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/yes.svg"></td>
                        <td><img src="/assets/img/no.svg"></td>
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
