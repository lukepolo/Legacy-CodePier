<template>
    <section class="events--item">
        <div class="events--item-status" :class="{'events--item-status-neutral' : event.status === 'Queued', 'events--item-status-success' : event.status === 'Completed', 'events--item-status-error' : event.status === 'Failed', 'icon-spinner' : event.status === 'Running'}"></div>
        <div class="events--item-name">
            <drop-down-event
                :title="eventTitle"
                :event="event"
                :prefix="event.id"
            >
                <template v-if="shouldGroupServers">
                    <template v-for="(commands, serverId) in servers">
                        <drop-down-event
                            :title="getServer(serverId, 'name') + ' (' + getServer(serverId, 'ip') + ')'"
                            :event="event"
                            :status="commandsStatus(commands)"
                            :prefix="'event_'+event.id+'_server_'+serverId"
                            :dropdown="true"
                        >
                            <template v-for="command in commands">
                                <drop-down-event
                                    :title="command.description"
                                    :event="command"
                                    :prefix="'command_'+command.id"
                                    :dropdown="getLog(command.log) ? getLog(command.log).length : false"
                                >
                                    <span v-html="getLog(command.log)"></span>
                                </drop-down-event>
                            </template>
                        </drop-down-event>
                    </template>
                </template>
                <template v-else>
                    <template v-for="command in event.server_commands">
                        <drop-down-event
                            :title="getServer(command.server_id, 'name') + ' (' + getServer(command.server_id, 'ip') + ')'"
                            :event="command"
                            :prefix="'command_'+command.id"
                            :dropdown="getLog(command.log) ? getLog(command.log).length : false"
                        >
                            <span v-html="getLog(command.log)"></span>
                        </drop-down-event>
                    </template>
                </template>
            </drop-down-event>
        </div>
        <template v-if="event.site_id">
            <div class="events--item-pile"><span class="icon-layers"></span> {{ getPile(getSite(event.site_id, 'pile_id'), 'name') }}</div>
            <div class="events--item-site"><span class="icon-browser"></span> {{ getSite(event.site_id, 'name') }}</div>
            <div class="events--item-commit"></div>
        </template>
        <template v-else>
            <div class="events--item-pile"><span class="icon-server"></span> {{ getServer(event.server_id, 'name') }}</div>
            <div class="events--item-site"></div>
        </template>
        <div class="events--item-time">
            <time-ago :time="event.created_at"></time-ago>
        </div>
    </section>
</template>

<script>
import DropDownEvent from "./DropDownEvent.vue";
export default {
  components: {
    DropDownEvent,
  },
  props: ["event"],
  methods: {
    getLog(log) {
      if (_.isArray(log)) {
        return _.join(_.map(log, "message"), "<br>");
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

      if (!isNaN(seconds)) {
        return seconds;
      }
    },
    commandsStatus(commands) {
      let total = commands.length;
      let failed = _.sumBy(commands, "failed");
      let started = _.sumBy(commands, "started");
      let completed = _.sumBy(commands, "completed");
      if (failed > 0) {
        return "events--item-status-error";
      } else if (total === completed) {
        return "events--item-status-success";
      } else if (started > 0) {
        return "icon-spinner";
      }
      return "events--item-status-neutral";
    },
  },
  computed: {
    eventTitle() {
      return this.event.description;
    },
    servers() {
      return _.groupBy(this.event.server_commands, "server_id");
    },
    shouldGroupServers() {
      let serverKeys = Object.keys(this.servers);
      if (serverKeys.length >= 1) {
        if (
          serverKeys.length > 1 ||
          Object.keys(_.values(this.servers)[0]).length > 1
        ) {
          return true;
        }
      }
      return false;
    },
  },
};
</script>
