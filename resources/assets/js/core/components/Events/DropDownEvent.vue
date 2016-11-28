<template>
    <section>

        <div class="event-status" :class="statusClass" v-if="statusClass"></div>

        <a class="collapsed" data-toggle="collapse" :href="'#' + eventName" v-if="showDropDown">
            <span class="icon-play"></span>
        </a>
        {{ title }}
        <div class="event-details collapse" :id="eventName">
            <slot></slot>
        </div>
    </section>
</template>

<script>
    export default {
        props : ['title', 'event', 'type', 'prefix', 'status', 'dropdown'],
        computed : {
            eventName : function() {
                return (this.prefix ? this.prefix : 'event') + '_' + this.event.id + '_' + this.type.replace(/\\/g, '_');
            },
            statusClass: function() {
                if(typeof this.status == 'undefined') {
                    if(this.event.hasOwnProperty('failed') && this.event.hasOwnProperty('completed') && this.event.hasOwnProperty('started')) {
                        if(!this.event.failed && ! this.event.completed && !this.event.started) {
                            return 'event-status-neutral';
                        } else if(this.event.completed) {
                            return 'event-status-success';
                        } else if(this.event.failed) {
                            return 'event-status-error';
                        } else if(!this.event.failed && !this.event.completed && this.event.started) {
                            return 'icon-spinner';
                        }
                    }
                }

                return this.status;
            },
            showDropDown() {
                return typeof this.dropdown != 'undefined' ? this.dropdown : true;
            }
        }
    }
</script>