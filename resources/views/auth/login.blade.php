@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">


            <div class="jcf-form-wrap">
                <div class="heading">
                    <h2>Create Account</h2>
                    <p>Fill out the following fields to create your account.</p>
                </div>

                <form action="#0" method="post" class="validation-form floating-labels">
                    <div class="jcf-input-group">
                        <input type="text" id="firstName" name="firstName" required>
                        <label for="firstName"><span class="float-label">First Name</span></label>
                    </div>
                    <div class="jcf-input-group">
                        <input type="text" id="lastName" name="lastName" required>
                        <label for="lastName"><span class="float-label">Last Name</span></label>
                    </div>
                    <div class="jcf-input-group">
                        <input type="email" id="email" name="email" required>
                        <label for="email"><span class="float-label">Email</span></label>
                    </div>
                    <div class="jcf-input-group">
                        <input type="password" id="password" name="password" required>
                        <label for="password"><span class="float-label">Password</span></label>
                    </div>

                    <hr>

                    <h3>Tell Us a Bit More About You:</h3>

                    <div class="jcf-input-group input-checkbox">
                        <div class="input-question">Which Fruits Do You Like?</div>
                        <label>
                            <input type="checkbox" id="fruits1" name="fruits" required>
                            <span class="icon"></span>Apples
                        </label>
                        <label>
                            <input type="checkbox" id="fruits2" name="fruits" required>
                            <span class="icon"></span>Pears
                        </label>
                        <label>
                            <input type="checkbox" id="fruits3" name="fruits" required>
                            <span class="icon"></span>Bananas
                        </label>
                        <label>
                            <input type="checkbox" id="fruits4" name="fruits" required>
                            <span class="icon"></span>Passion Fruit
                        </label>
                    </div>

                    <div class="jcf-input-group input-radio">
                        <div class="input-question">What is your favorite season?</div>
                        <label>
                            <input type="radio" id="season1" name="season" required>
                            <span class="icon"></span>Summer
                        </label>
                        <label>
                            <input type="radio" id="season2" name="season" required>
                            <span class="icon"></span>Fall
                        </label>
                        <label>
                            <input type="radio" id="season3" name="season" required>
                            <span class="icon"></span>Winter
                        </label>
                        <label>
                            <input type="radio" id="season4" name="season" required>
                            <span class="icon"></span>Spring
                        </label>
                    </div>

                    <hr>

                    <h3>Getting a Bit Personal</h3>

                    <div class="jcf-input-group">
                        <div class="input-question">What did you grow up?</div>
                        <div class="select-wrap">
                            <select>
                                <option value="">Alabama</option>
                                <option value="">Alaska</option>
                                <option value="">Arizona</option>
                                <option value="">Arkansas</option>
                                <option value="">California</option>
                                <option value="">Colorado</option>
                                <option value="">Connecticuit</option>
                                <option value="">Delaware</option>
                                <option value="">Florida</option>
                                <option value="">Georgia</option>
                                <option value="">Hawaii</option>
                                <option value="">Idaho</option>
                                <option value="">Illinois</option>
                                <option value="">Indiana</option>
                            </select>
                        </div>
                    </div>
                </form>

                <div class="btn-footer">
                    <button class="btn">Cancel</button>
                    <button class="btn btn-primary" type="submit">Sign Up</button>
                </div>
            </div><!-- end form-wrap -->


        </div>
        <h5 class="text-center"> - Or sign in using -</h5>
        <ul class="list-inline text-center">
            <li>
                <a href="{{ action('Auth\OauthController@newProvider', 'github') }}" class="btn btn-primary btn-circle"><i class="fa fa-github"></i></a>
            </li>
            <li>
                <a href="{{ action('Auth\OauthController@newProvider', 'bitbucket') }}" class="btn btn-primary btn-circle"><i class="fa fa-bitbucket"></i></a>
            </li>
            <li>
                <a href="{{ action('Auth\OauthController@newProvider', 'digitalocean') }}" class="btn btn-primary btn-circle"><i class="fa fa-server"></i></a>
            </li>
        </ul>
    </div>
</div>
@endsection
