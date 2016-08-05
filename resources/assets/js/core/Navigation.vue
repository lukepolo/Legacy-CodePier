<template>
    <header>
        <div class="logo-container">
            <img src="/assets/img/kodi_h.svg" alt="CodePier">
        </div>

        <ul class="nav nav-left nav-piles">
            <li class="dropdown arrow">
                <a href="#" class="dropdown-toggle" id="drop1" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="icon-layers"></span> Dev
                </a>

                <ul class="dropdown-menu" aria-labelledby="drop1">
                    <li>
                        <a href="#" class="selected"><span class="icon-layers"></span> Dev</a>
                    </li>
                    <li>
                        <a href="#"><span class="icon-layers"></span> QA</a>
                    </li>
                    <li>
                        <a href="#"><span class="icon-layers"></span> Prod</a>
                    </li>
                </ul>
            </li>
        </ul>

        <ul class="nav navbar-right nav-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" id="drop2" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                    <span class="muted">Team:</span> Private <span class="icon-arrow-down"></span>
                </a>

                <ul class="dropdown-menu" aria-labelledby="drop2">
                    <li>
                        <span class="dropdown-heading">Change Team</span>
                    </li>
                    <li>
                        <a href="#" class="selected">Private</a>
                    </li>
                    <template v-for="team in user.teams">
                        <li>
                            <a href="#">{{ team.name }}</a>
                        </li>
                    </template>
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" id="drop1" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="icon-settings"></span>
                </a>

                <ul class="dropdown-menu" aria-labelledby="drop1">
                    <li>
                        <a href="#"><span class="icon-person"></span> My Profile</a>
                    </li>
                    <li>
                        <a href="#"><span class="icon-people"></span> Manage Teams</a>
                    </li>
                    <li>
                        <a href="#"><span class="icon-layers"></span> My Piles</a>
                    </li>
                    <li>
                        <a href="#"><span class="icon-power"></span> Logout</a>
                    </li>
                </ul>
            </li>
        </ul>
    </header>
</template>

<script>
    export default {
        data() {
            return {
                user : user
            }
        }
    }
</script>