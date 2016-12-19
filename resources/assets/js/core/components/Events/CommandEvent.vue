<template>
    <section class="event">
        <div class="event-status" :class="{'event-status-neutral' : event.status == 'Queued', 'event-status-success' : event.status == 'Completed', 'event-status-error' : event.status == 'Failed', 'icon-spinner' : event.status == 'Running'}"></div>
        {{ event.status }}
        <div class="event-name">
            <drop-down-event
                    :title="eventTitle"
                    :event="event"
                    :type="event.commandable_type"
                    :prefix="event.id"
            >
                <template v-for="command in event.server_commands">
                    <drop-down-event
                            :title="getServer(command.server_id, 'name') + ' (' + getServer(command.server_id, 'ip') + ')'"
                            :event="command"
                            :type="event.commandable_type"
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
        <template v-if="event.site_id">
                <div class="event-pile"><span class="icon-layers"></span> {{ getPile(getSite(event.site_id, 'pile_id'), 'name') }}</div>
                <div class="event-site"><span class="icon-browser"></span> {{ getSite(event.site_id, 'name') }}</div>
        </template>
        <template v-else>
            <div class="event-pile"><span class="icon-layers"></span> {{ getPile(getServer(event.server_id, 'pile_id'), 'name') }}</div>
            <div class="event-site"><span class="icon-browser"></span> {{ getServer(event.server_id, 'name') }}</div>
        </template>
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
        },
        computed : {
            eventTitle() {
                var str = this.event.commandable_type;
                var title = str.substring(str.lastIndexOf('\\') + 1);

                return title.replace(/([A-Z])/g, ' $1').replace(/^./, function(str) {
                    return str.toUpperCase();
                });
            }
        }
    }
</script>