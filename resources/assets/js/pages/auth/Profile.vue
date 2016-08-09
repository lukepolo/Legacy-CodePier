<template>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Your Profile</div>
                    <div class="panel-body">
                        <ul id="myTabs" class="nav nav-tabs">
                            <li role="presentation" class="active"><a href="#account-info" role="tab" data-toggle="tab">AccountInfo</a></li>
                            <li role="presentation" class=""><a href="#ssh-keys" role="tab" data-toggle="tab">SSHKeys</a></li>
                            <li role="presentation" class=""><a href="#server-providers" role="tab" data-toggle="tab">ServerProviders</a></li>
                            <li role="presentation" class=""><a href="#repository-providers" role="tab" data-toggle="tab">Repository Providers</a></li>
                            <li role="presentation" class=""><a href="#notification-providers" role="tab" data-toggle="tab">Notification Providers</a></li>
                            <li role="presentation" class=""><a href="#subscription" role="tab" data-toggle="tab">Subscription</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade active in" id="account-info">
                                <user-info :user="user"></user-info>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="ssh-keys">
                                <user-ssh-keys></user-ssh-keys>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="server-providers">
                                <user-server-providers></user-server-providers>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="repository-providers">
                                <user-repository-providers></user-repository-providers>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="notification-providers">
                                <user-notification-providers></user-notification-providers>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="subscription">
                                <user-subscription :user="user"></user-subscription>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        $(document).on('click', '.new-card', function() {
            $('#card-info').toggleClass('hide');
        });
    </div>
</template>


<script>

    import UserInfo from './components/UserInfo.vue';
    import UserSshKeys from './components/UserSSHKeys.vue';
    import UserSubscription from './components/UserSubscription.vue';
    import UserServerProviders from './components/UserServerProviders.vue';
    import UserRepositoryProviders from './components/UserRepositoryProvders.vue';
    import UserNotificationProviders from './components/UserNotificationProviders.vue';

    export default {
        components: {
            UserInfo,
            UserSshKeys,
            UserSubscription,
            UserServerProviders,
            UserRepositoryProviders,
            UserNotificationProviders
        },
        data() {
            return {
                user: user
            }
        }
    }
</script>