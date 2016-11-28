<template>
    <section class="event">
        <div class="event-status" :class="{'event-status-neutral' : (!event.started && !event.completed && !event.failed), 'event-status-success' : event.completed, 'event-status-error' : event.failed, 'icon-spinner' : (event.started && !event.completed && !event.failed) }"></div>
        <div class="event-name">
            <drop-down-event
                    :title="event.type"
                    :event="event"
                    :type="event.type"
                    :prefix="event.id"
            >
                <template v-for="command in event.server_commands">
                    <drop-down-event
                            :title="command.server.name + ' (' + command.server.ip + ')'"
                            :event="command"
                            :type="event.type"
                            :prefix="'command_'+command.id"
                            :dropdown="command.failed"
                    >
                        <template v-if="command.failed">
                            {{ command.log[0].log }}
                        </template>
                    </drop-down-event>
                </template>
            </drop-down-event>
        </div>
        <div class="event-pile"><span class="icon-layers"></span> {{ event.site.pile.name }}</div>
        <div class="event-site"><span class="icon-browser"></span> {{ event.site.name }}</div>
    </section>
</template>

<script>
    import DropDownEvent from './DropDownEvent.vue';
    export default {
        components : {
            DropDownEvent,
        },
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