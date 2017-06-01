<template>
    <section class="events--item">
        <div class="events--item-status" :class="{'events--item-status-neutral' : event.status === 'Queued', 'events--item-status-success' : event.status === 'Completed', 'events--item-status-error' : event.status === 'Failed', 'icon-spinner' : event.status === 'Running'}"></div>
        <div class="events--item-name">
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
                            <span v-html="getLog(command.log)"></span>
                        </template>
                    </drop-down-event>
                </template>
            </drop-down-event>
        </div>
        <template v-if="event.site_id">
                <div class="events--item-pile"><span class="icon-layers"></span> {{ getPile(getSite(event.site_id, 'pile_id'), 'name') }}</div>
                <div class="events--item-site"><span class="icon-browser"></span> {{ getSite(event.site_id, 'name') }}</div>
            <div class="events--item-commit"></div>
        </template>
        <template v-else>
            <div class="events--item-pile"><span class="icon-layers"></span> {{ getPile(getServer(event.server_id, 'pile_id'), 'name') }}</div>
            <div class="events--item-site"><span class="icon-browser"></span> {{ getServer(event.server_id, 'name') }}</div>
            <div class="events--item-commit"></div>
        </template>
        <div class="events--item-time">{{ timeAgo(event.created_at) }}</div>
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
            getLog(log) {

                if(_.isArray(log)) {
                    return _.join(_.map(log, 'message'), '<br>');
                }

                return log;
            },
            filterArray(data) {
                if (Array.isArray(data.log)) {
                    return data.log.filter(String);
                }
                return [];
            },
            formatSeconds(number) {
                let seconds = parseFloat(number).toFixed(2);

                if(!isNaN(seconds)) {
                    return seconds;
                }
            }
        },
        computed : {
            eventTitle() {
                let str = this.event.commandable_type;
                let title = str.substring(str.lastIndexOf('\\') + 1);

                return title.replace(/([A-Z])/g, ' $1').replace(/^./, function(str) {
                    return str.toUpperCase();
                });
            }
        }
    }
</script>