@extends('layouts.public')

@section('content')
    <section id="cover-main" class="cover">
        <div class="cover--content">
            <div class="cover--logo-container">
                <img src="assets/img/codepier.svg">
            </div>
            <h1>You Build it. We Deploy it.</h1>
            <p>You're here to build apps. CodePier is here to help you manage your infrastructure, allow custom provisioning for each application, and eliminate downtime with zerotime deployments, plus, so much more. Come check it out.</p>


            <div class="jcf-form-wrap">
                <form>
                    <div class="jcf-input-group">
                        <input type="email" id="email" name="email" required="">
                        <label for="email"><span class="float-label">Enter your email to join our beta</span></label>
                    </div>
                </form>
            </div>

            <div class="btn-container">
                <a href="#" class="btn btn-primary btn-large">Join our Beta</a>
            </div>
        </div>
    </section>
    <section id="section-video" class="section">
        <div class="section--content">
            <div class="video">
                <iframe src="https://player.vimeo.com/video/205614363?color=48ACF0&title=0&byline=0&portrait=0" width="800" height="450" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            </div>
        </div>
    </section>
    <section id="comparison" class="section">
        <div class="section--content">
            <table class="table table--comparison">
                <tr>
                    <th class="row1"></th>
                    <th class="row2"><img src="/assets/img/codepier.svg" style="max-width: 130px;"></th>
                    <th class="row3">Forge</th>
                    <th class="row4">Heroku</th>
                    <th class="row5">Engine Yard</th>
                </tr>
                <tr>
                    <td>Runtime</td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                </tr>
                <tr>
                    <td>Scale</td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                </tr>
                <tr>
                    <td>Continuous Delivery</td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                </tr>
                <tr>
                    <td>Zerotime Deployments</td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                </tr>
                <tr>
                    <td>Team Management</td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                </tr>
                <tr>
                    <td>Custom Servers</td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                </tr>

                <tr>
                    <td>Continuous Delivery</td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                </tr>
                <tr>
                    <td>Zerotime Deployments</td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                </tr>
                <tr>
                    <td>Team Management</td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                </tr>
                <tr>
                    <td>Custom Servers</td>
                    <td><img src="/assets/img/yes.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                    <td><img src="/assets/img/no.svg"></td>
                </tr>
            </table>
            <div class="text-right">
                <p><em>* this is not a comprehensive list of features</em></p>
            </div>
        </div>
    </section>
@endsection
