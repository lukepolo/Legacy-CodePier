<template>
    <footer class="events" v-watch-scroll="{ events_pagination : events_pagination, form : form}" v-resizeable>
        <div id="drag" class="events--drag"></div>
        <div class="header events--header">
            <h4>
                <a class="toggle" @click="showEvents = !showEvents">
                    <span class="icon-warning"></span> Events
                </a>
            </h4>
        </div>
        <div class="events--collapse" :class="{ 'events--collapse-hidden' : !showEvents && windowWidth < 2100 }" id="collapseEvents">
            <ul class="filter">
                <li class="filter--label">
                    <span>Event Filters</span>
                </li>
                <li class="filter--item dropdown" ref="piles">
                    <a href="#" role="button" class="dropdown-toggle" @click="showFilter('piles')">
                        <strong>Pile:</strong>
                        <span class="filter--item-selection">
                            {{ pilesList }}
                        </span>
                        <span class="icon-arrow-up"></span>
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
                                <a class="btn btn-small" @click="cancel('piles')">Cancel</a>
                                <a class="btn btn-small btn-primary" @click="updateFilters">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

                <li class="filter--item dropdown" ref="sites">
                    <a href="#" role="button" class="dropdown-toggle" @click="showFilter('sites')">
                        <strong>Site:</strong> <span class="filter--item-selection">{{ siteList }}</span> <span class="icon-arrow-up"></span>
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
                                <a class="btn btn-small" @click="cancel('sites')">Cancel</a>
                                <a class="btn btn-small btn-primary" @click="updateFilters">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

                <li class="filter--item dropdown" ref="servers">
                    <a href="#" role="button" class="dropdown-toggle" @click="showFilter('servers')">
                        <strong>Server:</strong> <span class="filter--item-selection">{{ serverList }}</span> <span class="icon-arrow-up"></span>
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
                                <a class="btn btn-small" @click="cancel('servers')">Cancel</a>
                                <a class="btn btn-small btn-primary" @click="updateFilters">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

                <li class="filter--item dropdown" ref="types">
                    <a href="#" role="button" class="dropdown-toggle" @click="showFilter('types')">
                        <strong>Event Type:</strong> <span class="filter--item-selection">{{ eventList }}</span> <span class="icon-arrow-up"></span>
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
                                <a class="btn btn-small" @click="cancel('types')">Cancel</a>
                                <a class="btn btn-small btn-primary" @click="updateFilters">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>
            </ul>

            <div class="events--container">
                <section v-if="!events">
                    <div class="event--none">
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

                <div id="events-loading" class="hide" style="text-align: center;padding: 30px">
                    <span class="icon-spinner"></span>
                </div>
            </div>
        </div>

    </footer>
</template>

<script>

    import CommandEvent from './event-components/CommandEvent.vue';
    import DeploymentEvent from './event-components/DeploymentEvent.vue';

    Vue.directive('resizeable', {
        inserted: function (el, bindings) {

            let isResizing = false,
                lastOffset = null,
                container = $('footer'),
                bottom = $('#collapseEvents'),
                handle = $('#drag');

            handle.on('mousedown', function (e) {
                isResizing = true;
                bottom.addClass('dragging')
            });

            $(document).on('mousemove', function (e) {
                if (!isResizing) {
                    return;
                }

                lastOffset = container.height() - (e.clientY - container.offset().top);

                if(lastOffset < 100) {
                    lastOffset = 100
                }

                bottom.css('height', lastOffset - 40);
            }).on('mouseup', function (e) {
                isResizing = false;
                bottom.removeClass('dragging')
            });

        }
    });

    Vue.directive('watch-scroll', {
        update: function (el, bindings) {

            let container = $('#collapseEvents');

            container.unbind('scroll');

            let pagination = bindings.value.events_pagination;
            let form = bindings.value.form;

            if(pagination) {
                let nextPage = pagination.current_page + 1;
                if (nextPage <= pagination.last_page) {
                    container.bind('scroll', function() {
                        if ((container[0].scrollHeight - container[0].scrollTop - container[0].offsetHeight) < 1) {
                            form.page = nextPage;
                            $('#events-loading').removeClass('hide')
                            app.$store.dispatch('events/get', form).then((data) => {
                                $('#events-loading').addClass('hide')
                            });
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
                windowWidth : 0,
                showEvents : false,
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
                },
                prev_filters : {
                    types : {
                        commands : [],
                        site_deployments : [],
                    },
                    piles : [],
                    sites : [],
                    servers : [],
                }
            }
        },
        created() {
            this.fetchData();
        },
        mounted() {
            this.$nextTick(function() {
                window.addEventListener('resize', this.getWindowWidth);
                this.getWindowWidth()
            })

        },
        methods: {
            showFilter(filter) {
                let filterList = $(this.$refs[filter])
                $('#collapseEvents .dropdown').removeClass('open')
                filterList.toggleClass('open').find('.dropdown-menu').css('left', filterList.position().left)
            },
            fetchData() {
                this.$store.dispatch('events/get');
                this.$store.dispatch('user_sites/get');
                this.$store.dispatch('user_servers/get');
            },
            updateFilters() {
                this.$store.commit('events/clear');
                this.form.page = 1;

                this.prev_filters = _.cloneDeep(this.form.filters);
                this.$store.dispatch('events/get', this.form);
            },
            renderType(type) {
                let title = type.substring(type.lastIndexOf('\\') + 1);

                return title.replace(/([A-Z])/g, ' $1').replace(/^./, function(type) {
                    return type.toUpperCase();
                }) + 's';
            },
            cancel(type) {

                $('#collapseEvents .dropdown').removeClass('open')

                let filters = _.cloneDeep(this.prev_filters[type]);

                Vue.set(this.form.filters, type, filters);
            },
            getWindowWidth() {
                this.windowWidth = document.documentElement.clientWidth;
            },
        },
        computed: {
            pilesList() {
                let piles = this.form.filters.piles;
                if(!piles.length) {
                    return 'All';
                }

                return _.join(
                    _.map(piles, (pile) => {
                        return this.getPile(pile, 'name');
                        }
                    ), ', '
                );
            },
            siteList() {
                let sites = this.form.filters.sites;
                if(!sites.length) {
                    return 'All';
                }

                return _.join(
                    _.map(sites, (site) => {
                            return this.getSite(site, 'name');
                        }
                    ), ', '
                );
            },
            serverList() {
                let servers = this.form.filters.servers;
                if(!servers.length) {
                    return 'All';
                }

                return _.join(
                    _.map(servers, (server) => {
                            return this.getServer(server, 'name');
                        }
                    ), ', '
                );
            },
            eventList() {

                let types = this.form.filters.types;

                let events = _.map(_.merge(types.site_deployments, types.commands), (event) => {
                    return this.renderType(event);
                });

                if(!events.length) {
                    return 'All';
                }

                return _.join(events, ', ');
            },
            piles() {
                return this.$store.state.user_piles.piles;
            },
            sites() {
                return this.$store.state.user_sites.sites;
            },
            servers() {
                return this.$store.state.user_servers.servers;
            },

            events() {
                return this.$store.state.events.events;
            },
            events_pagination() {
                return this.$store.state.events.events_pagination;
            }
        },
    }

</script>