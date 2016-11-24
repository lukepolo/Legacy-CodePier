<template>
    <section class="event">
        <div class="event-status" :class="{'event-status-neutral' : (!event.started && !event.completed && !event.failed), 'event-status-success' : event.completed, 'event-status-error' : event.failed, 'icon-spinner' : (event.started && !event.completed && !event.failed) }"></div>
        <div class="event-name">
            <a class="collapsed" :class="{ 'in' : event.failed }" data-toggle="collapse" :href="'#command_event_' + event.id" v-if="event.log && filterArray(event.log).length">
                <span class="icon-play"></span>
            </a>
           {{ event.commandable_type }}

            <template v-if="event.completed">
                took {{ formatSeconds(event.runtime) }} seconds
            </template>

            <div class="event-details collapse" :class="{ 'in' : event.failed, 'out' : !event.failed }" :id="'command_event_'+event.id">
                <pre v-for="logObject in event.log" v-if="event.log">
                    {{ logObject.message }} {{ logObject.log }}
                </pre>
            </div>
        </div>
        <div class="event-pile"><span class="icon-layers"></span> {{ event.site.pile.name }}</div>
        <div class="event-site"><span class="icon-browser"></span> {{ event.site.name }}</div>
        <div class="event-site"><span class="icon-server"></span> {{ event.server.name }} ({{ event.server.ip }})</div>
    </section>
</template>

<script>
    export default {
        props : ['event'],
        methods: {
            filterArray(data) {
                if (Array.isArray(data.log)) {
                    return data.log.filter(String);
                }
                return [];
            },
            formatSeconds(number) {
                var seconds = parseFloat(number).toFixed(2);

                if(!isNaN(seconds)) {
                    return seconds;
                }
            }
        }
    }
</script>