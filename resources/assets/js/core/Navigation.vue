<template>
    <header>
        <div class="logo-container">
            <img src="/assets/img/kodi_h.svg" alt="CodePier">
        </div>

        <ul class="nav nav-left nav-piles ">
        </ul>

        <ul class="nav navbar-right nav-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" id="drop2" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                    <span class="muted">Team:</span> Private <span class="icon-arrow-down hide"></span>
                </a>

                <ul class="dropdown-menu hide" aria-labelledby="drop2">
                    <li>
                        <span class="dropdown-heading">Change Team</span>
                    </li>

                    <li>
                        <a href="#" class="selected">Private</a>
                    </li>
                    <li>
                        <a href="#">Patron Path</a>
                    </li>
                    <li>
                        <a href="#">Force</a>
                    </li>
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