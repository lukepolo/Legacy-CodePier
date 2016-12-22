<style>
    #drag {
        position: absolute;
        top: 0;
        bottom: 0;
        height: 10px;
        width: 100%;
        cursor: ns-resize;
    }
</style>
<template>
    <footer v-watch-scroll="{ events_pagination : events_pagination, form : form}" v-resizeable>
        <div id="drag"></div>
        <div class="header">
            <h4>
                <a class="toggle collapsed" data-toggle="collapse" href="#collapseEvents">
                    <span class="icon-warning"></span> Events
                </a>
            </h4>
        </div>

        <div class="collapse" id="collapseEvents">
            <ul class="filter">
                <li>
                    <span>Event Filters</span>
                </li>
                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true" class="dropdown-toggle">
                        <strong>Pile:</strong> <span class="filter-selection">Dev</span> <span class="icon-arrow-up"></span>
                    </a>

                    <ul class="dropdown-menu dropup">
                        <div class="jcf-form-wrap">
                            <form>
                                <div class="jcf-input-group input-checkbox">
                                    <label class="select-all">
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Select All
                                    </label>

                                    <label v-for="pile in piles">
                                        <input type="checkbox" v-model="form.filters.piles" :value="pile.id">
                                        <span class="icon"></span>
                                        {{ pile.name }}
                                    </label>
                                </div>
                            </form>

                            <div class="btn-footer">
                                <a class="btn btn-small">Cancel</a>
                                <a class="btn btn-small btn-primary" @click="updateFilters">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true" class="dropdown-toggle">
                        <strong>Site:</strong> <span class="filter-selection">All Sites</span> <span class="icon-arrow-up"></span>
                    </a>

                    <ul class="dropdown-menu dropup">
                        <div class="jcf-form-wrap">
                            <form>
                                <div class="jcf-input-group input-checkbox">
                                    <label class="select-all">
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Select All
                                    </label>
                                    <label v-for="site in sites">
                                        <input type="checkbox" v-model="form.filters.sites" :value="site.id">
                                        <span class="icon"></span>
                                        {{ site.name }}
                                    </label>
                                </div>
                            </form>

                            <div class="btn-footer">
                                <a class="btn btn-small">Cancel</a>
                                <a class="btn btn-small btn-primary" @click="updateFilters">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true" class="dropdown-toggle">
                        <strong>Server:</strong> <span class="filter-selection">All Servers</span> <span class="icon-arrow-up"></span>
                    </a>

                    <ul class="dropdown-menu dropup">
                        <div class="jcf-form-wrap">
                            <form>
                                <div class="jcf-input-group input-checkbox">
                                    <label class="select-all">
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Select All
                                    </label>
                                    <label v-for="server in servers">
                                        <input type="checkbox" v-model="form.filters.servers" :value="server.id">
                                        <span class="icon"></span>
                                        {{ server.name }} ({{ server.ip }})
                                    </label>
                                </div>
                            </form>

                            <div class="btn-footer">
                                <a class="btn btn-small">Cancel</a>
                                <a class="btn btn-small btn-primary" @click="updateFilters">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true" class="dropdown-toggle">
                        <strong>Event Type:</strong> <span class="filter-selection">Deployments</span> <span class="icon-arrow-up"></span>
                    </a>

                    <ul class="dropdown-menu dropup">
                        <div class="jcf-form-wrap">
                            <form>
                                <div class="jcf-input-group input-checkbox">
                                    <label class="select-all">
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Select All
                                    </label>

                                    <template v-for="(types, area) in defaultNotificationTypes">
                                        <template v-for="type in types">
                                            <label>
                                                <template v-if="area == 'site_deployments'">
                                                    <input type="checkbox" v-model="form.filters.types.site_deployments" :value="type">
                                                </template>
                                                <template v-else-if="area == 'commands'">
                                                    <input type="checkbox" v-model="form.filters.types.commands" :value="type">
                                                </template>
                                                <span class="icon"></span>
                                                {{ renderType(type) }}
                                            </label>
                                        </template>

                                    </template>
                                </div>
                            </form>

                            <div class="btn-footer">
                                <a class="btn btn-small">Cancel</a>
                                <a class="btn btn-small btn-primary" @click="updateFilters">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

            </ul>
            <div class="events-container">
                <section v-if="!events">
                    <div class="event-none">
                        There are no events with these filters.
                    </div>
                </section>
                <section v-else>
                    <template  v-for="event in events">
                        <template v-if="event.event_type == 'App\\Models\\Site\\SiteDeployment'">
                            <deployment-event :event="event" :key="event"></deployment-event>
                        </template>
                        <template v-else-if="event.event_type == 'App\\Models\\Command'">
                            <command-event :event="event" :key="event"></command-event>
                        </template>
                        <template v-else>
                            Invalid type {{ event.event_type }}
                        </template>
                    </template>
                </section>
            </div>
        </div>

    </footer>
</template>

<script>

    import CommandEvent from './components/Events/CommandEvent.vue';
    import DeploymentEvent from './components/Events/DeploymentEvent.vue';

    Vue.directive('resizeable', {
        inserted: function (el, bindings) {

            let isResizing = false,
                lastOffset = null,
                container = $('#app-layout'),
                top = $('#main'),
                bottom = $(el),
                handle = $('#drag');

            handle.on('mousedown', function (e) {
                isResizing = true;
            });

            $('#collapseEvents').on('hide.bs.collapse', function (el) {
                if($(el.target).attr('id') == 'collapseEvents') {
                    bottom.css('height', 'auto');
                }
            });

            $(document).on('mousemove', function (e) {
                if (!isResizing || !$('#collapseEvents').hasClass('in')) {
                    return;
                }

                lastOffset = container.height() - (e.clientY - container.offset().top);

                top.css('height', lastOffset);
                bottom.css('height', lastOffset);
            }).on('mouseup', function (e) {
                isResizing = false;
            });

        }
    });

    Vue.directive('watch-scroll', {
        update: function (el, bindings) {

            let container = $(el).find('.events-container');

            container.unbind('scroll');

            let pagination = bindings.value.events_pagination;
            let form = bindings.value.form;

            if(pagination) {
                let nextPage = pagination.current_page + 1;
                if (nextPage <= pagination.last_page) {
                    container.bind('scroll', function() {
                        if ((container[0].scrollHeight - container[0].scrollTop - container[0].offsetHeight) < 1) {
                            form.page = nextPage;
                            app.$store.dispatch('getEvents', form);
                        }
                    });
                }
            }
        }
    });

    export default {
        components : {
            CommandEvent,
            DeploymentEvent,
        },
        data() {
            return {
                defaultNotificationTypes : Laravel.defaultNotificationTypes,
                form : {
                    page : 1,
                    filters : {
                        types : {
                            commands : [],
                            site_deployments : [],
                        },
                        piles : [],
                        sites : [],
                        servers : [],
                    }
                }
            }
        },
        created() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getEvents');
                this.$store.dispatch('getAllUserPiles'); // todo - rename
                this.$store.dispatch('getAllSites');
                this.$store.dispatch('getAllServers');

                this.$store.dispatch('getRepositoryProviders');

            },
            updateFilters() {
                this.$store.commit('CLEAR_EVENTS');
                this.form.page = 1;
                this.$store.dispatch('getEvents', this.form);
            },
            renderType(type) {
                let title = type.substring(type.lastIndexOf('\\') + 1);

                return title.replace(/([A-Z])/g, ' $1').replace(/^./, function(type) {
                    return type.toUpperCase();
                }) + 's';
            }
        },
        computed: {
            piles() {
                return this.$store.state.pilesStore.all_user_piles;
            },
            sites() {
                return this.$store.state.sitesStore.all_sites;
            },
            servers() {
                return this.$store.state.serversStore.all_servers;
            },

            events() {
                return this.$store.state.eventsStore.events;
            },
            events_pagination() {
                return this.$store.state.eventsStore.events_pagination;
            },
        },
    }

</script>