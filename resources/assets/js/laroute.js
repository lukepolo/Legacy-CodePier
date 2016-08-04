(function () {

    var laroute = (function () {

        var routes = {

            absolute: true,
            rootUrl: 'http://codepier.app',
            routes : [{"host":null,"methods":["POST"],"uri":"broadcasting\/auth","name":null,"action":"Illuminate\Broadcasting\BroadcastController@authenticate"},{"host":null,"methods":["POST"],"uri":"broadcasting\/socket","name":null,"action":"Illuminate\Broadcasting\BroadcastController@rememberSocket"},{"host":null,"methods":["GET","HEAD"],"uri":"login","name":"login","action":"Auth\LoginController@showLoginForm"},{"host":null,"methods":["POST"],"uri":"login","name":null,"action":"Auth\LoginController@login"},{"host":null,"methods":["POST"],"uri":"logout","name":null,"action":"Auth\LoginController@logout"},{"host":null,"methods":["GET","HEAD"],"uri":"register","name":null,"action":"Auth\RegisterController@showRegistrationForm"},{"host":null,"methods":["POST"],"uri":"register","name":null,"action":"Auth\RegisterController@register"},{"host":null,"methods":["GET","HEAD"],"uri":"password\/reset","name":null,"action":"Auth\ForgotPasswordController@showLinkRequestForm"},{"host":null,"methods":["POST"],"uri":"password\/email","name":null,"action":"Auth\ForgotPasswordController@sendResetLinkEmail"},{"host":null,"methods":["GET","HEAD"],"uri":"password\/reset\/{token}","name":null,"action":"Auth\ResetPasswordController@showResetForm"},{"host":null,"methods":["POST"],"uri":"password\/reset","name":null,"action":"Auth\ResetPasswordController@reset"},{"host":null,"methods":["GET","HEAD"],"uri":"logout","name":null,"action":"Auth\LoginController@logout"},{"host":null,"methods":["GET","HEAD"],"uri":"\/","name":null,"action":"LandingController@getIndex"},{"host":null,"methods":["GET","HEAD"],"uri":"provider\/{provider}\/link","name":null,"action":"Auth\OauthController@newProvider"},{"host":null,"methods":["GET","HEAD"],"uri":"provider\/{provider}\/callback","name":null,"action":"Auth\OauthController@getHandleProviderCallback"},{"host":null,"methods":["GET","HEAD"],"uri":"webhook\/diskspace","name":null,"action":"Closure"},{"host":null,"methods":["GET","HEAD"],"uri":"webhook\/deploy\/{siteHashID}","name":"webhook\/deploy","action":"Closure"},{"host":null,"methods":["GET","HEAD"],"uri":"provider\/disconnect\/{serverProviderID}\/{serviceID}","name":null,"action":"Auth\OauthController@getDisconnectService"},{"host":null,"methods":["GET","HEAD"],"uri":"admin","name":null,"action":"AdminController@getIndex"},{"host":null,"methods":["GET","HEAD"],"uri":"admin\/server-provider\/{providerID}\/options-regions","name":null,"action":"AdminController@getServerOptionsAndRegions"},{"host":null,"methods":["GET","HEAD"],"uri":"my-profile","name":null,"action":"Auth\UserController@getMyProfile"},{"host":null,"methods":["POST"],"uri":"my-profile","name":null,"action":"Auth\UserController@postMyProfile"},{"host":null,"methods":["POST"],"uri":"my-profile\/add-ssh-key","name":null,"action":"Auth\UserController@postAddSshKey"},{"host":null,"methods":["GET","HEAD"],"uri":"my-profile\/remove-ssh-key\/{sshKeyID}","name":null,"action":"Auth\UserController@getRemoveSshKey"},{"host":null,"methods":["GET","HEAD"],"uri":"teams","name":"teams.index","action":"Teamwork\TeamController@index"},{"host":null,"methods":["GET","HEAD"],"uri":"teams\/create","name":"teams.create","action":"Teamwork\TeamController@create"},{"host":null,"methods":["POST"],"uri":"teams\/teams","name":"teams.store","action":"Teamwork\TeamController@store"},{"host":null,"methods":["GET","HEAD"],"uri":"teams\/edit\/{id}","name":"teams.edit","action":"Teamwork\TeamController@edit"},{"host":null,"methods":["PUT"],"uri":"teams\/edit\/{id}","name":"teams.update","action":"Teamwork\TeamController@update"},{"host":null,"methods":["DELETE"],"uri":"teams\/destroy\/{id}","name":"teams.destroy","action":"Teamwork\TeamController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"teams\/switch\/{id?}","name":"teams.switch","action":"Teamwork\TeamController@switchTeam"},{"host":null,"methods":["GET","HEAD"],"uri":"teams\/members\/{id}","name":"teams.members.show","action":"Teamwork\TeamMemberController@show"},{"host":null,"methods":["GET","HEAD"],"uri":"teams\/members\/resend\/{invite_id}","name":"teams.members.resend_invite","action":"Teamwork\TeamMemberController@resendInvite"},{"host":null,"methods":["POST"],"uri":"teams\/members\/{id}","name":"teams.members.invite","action":"Teamwork\TeamMemberController@invite"},{"host":null,"methods":["DELETE"],"uri":"teams\/members\/{id}\/{user_id}","name":"teams.members.destroy","action":"Teamwork\TeamMemberController@destroy"},{"host":null,"methods":["GET","HEAD"],"uri":"teams\/accept\/{token}","name":"teams.accept_invite","action":"Teamwork\AuthController@acceptInvite"},{"host":null,"methods":["POST"],"uri":"subscription","name":null,"action":"PaymentController@postSubscription"},{"host":null,"methods":["GET","HEAD"],"uri":"subscription\/cancel","name":null,"action":"PaymentController@getCancelSubscription"},{"host":null,"methods":["GET","HEAD"],"uri":"subscription\/invoice\/{invoiceID}","name":null,"action":"PaymentController@getUserInvoice"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}","name":null,"action":"ServerController@getServer"},{"host":null,"methods":["POST"],"uri":"create-server","name":null,"action":"ServerController@postCreateServer"},{"host":null,"methods":["GET","HEAD"],"uri":"servers\/archive","name":null,"action":"ServerController@getArchivedServers"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/archive","name":null,"action":"ServerController@getArchiveServer"},{"host":null,"methods":["GET","HEAD"],"uri":"servers\/archive\/{serverID}\/activate","name":null,"action":"ServerController@getActivateArchivedServer"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/check-connection","name":null,"action":"ServerController@getTestSshConnection"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/ssh-key\/install","name":null,"action":"ServerController@postInstallSshKey"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/ssh-key\/{serverSshKeyId}\/remove","name":null,"action":"ServerController@getRemoveSshKey"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/cron-job\/install","name":null,"action":"ServerController@postInstallCronJob"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/cron-job\/{cronJobID}\/remove","name":null,"action":"ServerController@getRemoveCronJob"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/firewall-rule\/add","name":null,"action":"ServerController@postAddFirewallRule"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/server-network-rules\/add","name":null,"action":"ServerController@postAddServerNetworkRules"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/firewall-rule\/{fireWallID}\/remove","name":null,"action":"ServerController@getRemoveFireWallRule"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/daemon\/add","name":null,"action":"ServerController@postAddDaemon"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/daemon\/{daemonID}\/remove","name":null,"action":"ServerController@getRemoveDaemon"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/restart\/server","name":null,"action":"ServerController@getRestartServer"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/restart\/workers","name":null,"action":"ServerController@getRestartWorkers"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/restart\/database","name":null,"action":"ServerController@getRestartDatabase"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/restart\/web-server","name":null,"action":"ServerController@getRestartWebServices"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/get-file","name":null,"action":"ServerController@getFileFromServer"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/file\/save","name":null,"action":"ServerController@postSaveFile"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/install\/blackfire","name":null,"action":"ServerController@postInstallBlackfire"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/site\/{siteID}","name":null,"action":"SiteController@getSite"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/create-site","name":null,"action":"SiteController@postCreateSite"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/site\/{siteID}\/delete","name":null,"action":"SiteController@getDeleteSite"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/site\/{siteID}\/deploy","name":null,"action":"SiteController@getDeploy"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/site\/{siteID}\/deploy\/hook","name":null,"action":"SiteController@getCreateDeployHook"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/site\/{siteID}\/deploy\/hook\/delete","name":null,"action":"SiteController@getDeleteDeployHook"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/site\/{siteID}\/env-file","name":null,"action":"SiteController@getEnv"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/site\/{siteID}\/install-repository","name":null,"action":"SiteController@postInstallRepository"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/site\/{siteID}\/repository\/remove","name":null,"action":"SiteController@getRemoveRepository"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/site\/{siteID}\/ssl\/remove","name":null,"action":"SiteController@getRemoveSSL"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/site\/{siteID}\/domain\/rename","name":null,"action":"SiteController@postRenameDomain"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/site\/{siteID}\/ssl\/lets-encrypt","name":null,"action":"SiteController@postRequestLetsEncryptSSLCert"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/site\/{siteID}\/env","name":null,"action":"SiteController@postEnv"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/site\/{siteID}\/install-worker","name":null,"action":"SiteController@postInstallWorker"},{"host":null,"methods":["GET","HEAD"],"uri":"server\/{serverID}\/site\/{siteID}\/remove-worker\/{workerID}","name":null,"action":"SiteController@getRemoveWorker"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/site\/{siteID}\/custom-settings","name":null,"action":"SiteController@postSavePHPSettings"},{"host":null,"methods":["POST"],"uri":"server\/{serverID}\/site\/{siteID}\/update-web-directory","name":null,"action":"SiteController@postUpdateWebDirectory"},{"host":null,"methods":["POST"],"uri":"stripe\/webhook","name":null,"action":"\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook"},{"host":null,"methods":["GET","HEAD"],"uri":"api\/user","name":null,"action":"Closure"},{"host":null,"methods":["GET","HEAD"],"uri":"_debugbar\/open","name":"debugbar.openhandler","action":"Barryvdh\Debugbar\Controllers\OpenHandlerController@handle"},{"host":null,"methods":["GET","HEAD"],"uri":"_debugbar\/clockwork\/{id}","name":"debugbar.clockwork","action":"Barryvdh\Debugbar\Controllers\OpenHandlerController@clockwork"},{"host":null,"methods":["GET","HEAD"],"uri":"_debugbar\/assets\/stylesheets","name":"debugbar.assets.css","action":"Barryvdh\Debugbar\Controllers\AssetController@css"},{"host":null,"methods":["GET","HEAD"],"uri":"_debugbar\/assets\/javascript","name":"debugbar.assets.js","action":"Barryvdh\Debugbar\Controllers\AssetController@js"}],
            prefix: '',

            route : function (name, parameters, route) {
                route = route || this.getByName(name);

                if ( ! route ) {
                    return undefined;
                }

                return this.toRoute(route, parameters);
            },

            url: function (url, parameters) {
                parameters = parameters || [];

                var uri = url + '/' + parameters.join('/');

                return this.getCorrectUrl(uri);
            },

            toRoute : function (route, parameters) {
                var uri = this.replaceNamedParameters(route.uri, parameters);
                var qs  = this.getRouteQueryString(parameters);

                return this.getCorrectUrl(uri + qs);
            },

            replaceNamedParameters : function (uri, parameters) {
                uri = uri.replace(/\{(.*?)\??\}/g, function(match, key) {
                    if (parameters.hasOwnProperty(key)) {
                        var value = parameters[key];
                        delete parameters[key];
                        return value;
                    } else {
                        return match;
                    }
                });

                // Strip out any optional parameters that were not given
                uri = uri.replace(/\/\{.*?\?\}/g, '');

                return uri;
            },

            getRouteQueryString : function (parameters) {
                var qs = [];
                for (var key in parameters) {
                    if (parameters.hasOwnProperty(key)) {
                        qs.push(key + '=' + parameters[key]);
                    }
                }

                if (qs.length < 1) {
                    return '';
                }

                return '?' + qs.join('&');
            },

            getByName : function (name) {
                for (var key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].name === name) {
                        return this.routes[key];
                    }
                }
            },

            getByAction : function(action) {
                for (var key in this.routes) {
                    if (this.routes.hasOwnProperty(key) && this.routes[key].action === action) {
                        return this.routes[key];
                    }
                }
            },

            getCorrectUrl: function (uri) {
                var url = this.prefix + '/' + uri.replace(/^\/?/, '');

                if(!this.absolute)
                    return url;

                return this.rootUrl.replace('/\/?$/', '') + url;
            }
        };

        var getLinkAttributes = function(attributes) {
            if ( ! attributes) {
                return '';
            }

            var attrs = [];
            for (var key in attributes) {
                if (attributes.hasOwnProperty(key)) {
                    attrs.push(key + '="' + attributes[key] + '"');
                }
            }

            return attrs.join(' ');
        };

        var getHtmlLink = function (url, title, attributes) {
            title      = title || url;
            attributes = getLinkAttributes(attributes);

            return '<a href="' + url + '" ' + attributes + '>' + title + '</a>';
        };

        return {
            // Generate a url for a given controller action.
            // laroute.action('HomeController@getIndex', [params = {}])
            action : function (name, parameters) {
                parameters = parameters || {};

                return routes.route(name, parameters, routes.getByAction(name));
            },

            // Generate a url for a given named route.
            // laroute.route('routeName', [params = {}])
            route : function (route, parameters) {
                parameters = parameters || {};

                return routes.route(route, parameters);
            },

            // Generate a fully qualified URL to the given path.
            // laroute.route('url', [params = {}])
            url : function (route, parameters) {
                parameters = parameters || {};

                return routes.url(route, parameters);
            },

            // Generate a html link to the given url.
            // laroute.link_to('foo/bar', [title = url], [attributes = {}])
            link_to : function (url, title, attributes) {
                url = this.url(url);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given route.
            // laroute.link_to_route('route.name', [title=url], [parameters = {}], [attributes = {}])
            link_to_route : function (route, title, parameters, attributes) {
                var url = this.route(route, parameters);

                return getHtmlLink(url, title, attributes);
            },

            // Generate a html link to the given controller action.
            // laroute.link_to_action('HomeController@getIndex', [title=url], [parameters = {}], [attributes = {}])
            link_to_action : function(action, title, parameters, attributes) {
                var url = this.action(action, parameters);

                return getHtmlLink(url, title, attributes);
            }

        };

    }).call(this);

    /**
     * Expose the class either via AMD, CommonJS or the global object
     */
    if (typeof define === 'function' && define.amd) {
        define(function () {
            return laroute;
        });
    }
    else if (typeof module === 'object' && module.exports){
        module.exports = laroute;
    }
    else {
        window.laroute = laroute;
    }

}).call(this);

