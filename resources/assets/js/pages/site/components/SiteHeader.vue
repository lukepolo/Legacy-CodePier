<template>
    <section>
        <h3 class="section-header primary" v-if="site">
            {{ site.name }}
            <div class="pull-right">
                <div class="dropdown">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-server"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="#">Restart Web Services</a></li>
                        <li><a href="#">Restart Servers</a></li>
                        <li><a href="#">Restart Databases</a></li>
                        <li><a href="#">Restart Workers</a></li>
                    </ul>
                </div>
            </div>
            <div class="pull-right">
                <div class="dropdown">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-files-o"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="#">Edit Nginx Config</a></li>
                        <li><a href="#">Edit PHP Config</a></li>
                    </ul>
                </div>
            </div>
        </h3>
    </section>
</template>

<script>
    export default {
        computed : {
            site : () => {
                return siteStore.state.site;
            }
        }
    }
</script>