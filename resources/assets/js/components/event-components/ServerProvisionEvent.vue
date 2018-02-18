<template>
    <section class="events--item">
        <div class="events--item-status" :class="{'events--item-status-neutral' : event.progress === 0, 'events--item-status-success' : event.progress >= 100, 'events--item-status-error' : event.status === 'Failed', 'icon-spinner' : event.progress < 100 }"></div>
        <div class="events--item-name">
            <drop-down-event
                :title="eventTitle"
                :event="event"
                :prefix="event.id"
            >
                <template v-for="provision_step in event.provision_steps">
                    <drop-down-event
                        :title="provision_step.step"
                        :event="provision_step"
                        :prefix="'provision_step_'+provision_step.id"
                        :dropdown="getLog(provision_step.log) ? getLog(provision_step.log).length : false"
                    >
                        <span v-html="getLog(provision_step.log)"></span>
                    </drop-down-event>
                </template>
            </drop-down-event>
        </div>
        <div class="events--item-pile"><span class="icon-server"></span> {{ event.name }}</div>
        <div class="events--item-commit"></div>
        <div class="events--item-time">
            <time-ago :time="event.created_at"></time-ago>
        </div>
    </section>
</template>

<script>
  import DropDownEvent from "./DropDownEvent.vue";
  export default {
    components: {
      DropDownEvent
    },
    props: ["event"],
    methods: {
      getLog(log) {
        if (_.isArray(log)) {
          return _.join(_.map(log, "message"), "<br>");
        }

        return log;
      },
    },
    computed: {
      eventTitle() {
        return `Provisioning ${this.event.name} (${this.event.ip})`;
      }
    }
  };
</script>
