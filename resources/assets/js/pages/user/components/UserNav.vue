<template>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <ul id="myTabs" class="nav nav-tabs">
                            <router-link role="presentation" tag="li" to="/my-profile" exact><a>My Profile</a>
                            </router-link>
                            <router-link role="presentation" tag="li" to="/my-profile/oauth"><a>OAuth</a></router-link>
                            <router-link role="presentation" tag="li" to="/my-profile/ssh-keys"><a>SSH Keys</a>
                            </router-link>
                            <router-link role="presentation" tag="li" to="/my-profile/subscription"><a>Subscription</a>
                            </router-link>
                            <router-link role="presentation" tag="li" to="/my-profile/server-providers"><a>Server
                                Providers</a></router-link>
                            <router-link role="presentation" tag="li" to="/my-profile/repository-providers"><a>Repository
                                Providers</a></router-link>
                            <router-link role="presentation" tag="li" to="/my-profile/notification-providers"><a>Notification
                                Providers</a></router-link>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>