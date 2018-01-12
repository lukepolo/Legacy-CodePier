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
        <div class="events--collapse" :class="{ 'events--collapse-hidden' : !showEvents && windowWidth < 2100 }"
             id="collapseEvents">
            <ul class="filter">
                <li class="filter--label">
                    <span>Event Filters</span>
                </li>
                <event-filter title="Piles" :filters="piles" :selected-filters.sync="form.filters.piles"></event-filter>
                <event-filter title="Sites" :filters="sites" :selected-filters.sync="form.filters.sites"></event-filter>
                <event-filter title="Servers" :filters="servers" :selected-filters.sync="form.filters.servers"></event-filter>
                <event-filter title="Events" :filters="servers" :selected-filters.sync="form.filters.events"></event-filter>
            </ul>

            <div class="events--container">
                <section v-if="!events">
                    <div class="event--none">
                        There are no events with these filters.
                    </div>
                </section>
                <section v-else>
                    <template v-for="event in events">
                        <template v-if="event.event_type === 'App\\Models\\Site\\SiteDeployment'">
                            <deployment-event :event="event"
                                              :key="event.event_type + '\\'  + event.id"></deployment-event>
                        </template>
                        <template v-else-if="event.event_type === 'App\\Models\\Command'">
                            <command-event :event="event" :key="event.event_type + '\\'  + event.id"></command-event>
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
    import EventFilter from "./event-components/EventFilter.vue";
    import CommandEvent from "./event-components/CommandEvent.vue";
    import DeploymentEvent from "./event-components/DeploymentEvent.vue";

    Vue.directive("resizeable", {
        inserted: function (el, bindings) {
            const container = $("footer");
            const bottom = $("#collapseEvents");
            const handle = $("#drag");

            let isResizing = false;
            let lastOffset = null;

            handle.on("mousedown", function (e) {
                isResizing = true;
                bottom.addClass("dragging");
            });

            $(document)
                .on("mousemove", function (e) {
                    if (!isResizing) {
                        return;
                    }

                    lastOffset = container.height() - (e.clientY - container.offset().top);

                    if (lastOffset < 100) {
                        lastOffset = 100;
                    }

                    bottom.css("height", lastOffset - 40);
                })
                .on("mouseup", function (e) {
                    isResizing = false;
                    bottom.removeClass("dragging");
                });
        }
    });

    Vue.directive("watch-scroll", {
        update: function (el, bindings) {
            const container = $("#collapseEvents");

            container.unbind("scroll");

            const pagination = bindings.value.events_pagination;
            const form = bindings.value.form;

            if (pagination) {
                const nextPage = pagination.current_page + 1;
                if (nextPage <= pagination.last_page) {
                    container.bind("scroll", function () {
                        if (
                            container[0].scrollHeight -
                            container[0].scrollTop -
                            container[0].offsetHeight <
                            1
                        ) {
                            if (form.page !== nextPage) {
                                form.page = nextPage;
                                $("#events-loading").removeClass("hide");

                                app.$store.dispatch("events/get", form).then(data => {
                                    $("#events-loading").addClass("hide");
                                });
                            }
                        }
                    });
                }
            }
        }
    });

    export default {
        components: {
            EventFilter,
            CommandEvent,
            DeploymentEvent
        },
        data() {
            return {
                windowWidth: 0,
                showEvents: false,
                defaultNotificationTypes: Laravel.defaultNotificationTypes,
                form: this.createForm({
                    page: 1,
                    filters: {
                        types: {
                            commands: [],
                            site_deployments: []
                        },
                        piles: [],
                        sites: [],
                        servers: []
                    }
                }),
            };
        },
        created() {
            this.fetchData();
        },
        mounted() {
            this.$nextTick(function () {
                window.addEventListener("resize", this.getWindowWidth);
                this.getWindowWidth();
            });
        },
        watch : {
            'form.filters' : {
              deep : true,
              handler : function() {
                this.form.page = 1;
                this.$store.commit("events/clear");
                this.$store.dispatch("events/get", this.form);
              }
            }
        },
        methods: {
            fetchData() {
                this.$store.dispatch("events/get");
                this.$store.dispatch("user_servers/get");
            },
            // renderType(type) {
            //     const title = type.substring(type.lastIndexOf("\\") + 1);
            //     return (
            //         title.replace(/([A-Z])/g, " $1").replace(/^./, function (type) {
            //             return type.toUpperCase();
            //         }) + "s"
            //     );
            // },
            getWindowWidth() {
                this.windowWidth = document.documentElement.clientWidth;
            }
        },
        computed: {
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
                return _.orderBy(
                    _.uniqBy(this.$store.state.events.events, event => {
                        return event.event_type + event.id;
                    }),
                    event => {
                        return event.created_at;
                    },
                    "desc"
                );
            },
            events_pagination() {
                return this.$store.state.events.events_pagination;
            }
        }
    };
</script>
