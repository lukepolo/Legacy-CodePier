<template>
    <section>

        <div class="events--item-status" :class="statusClass" v-if="statusClass"></div>

        <a :class="{ collapsed : !show }" :href="'#' + eventName" v-if="showDropDown" @click="toggle">
            <span class="icon-play"></span>
        </a>
        {{ title }}
        <transition name="collapse">
            <div class="events--item-details" :id="eventName" v-show="show">
                <slot></slot>
            </div>
        </transition>
    </section>
</template>

<script>
    export default {
        props : ['title', 'event', 'type', 'prefix', 'status', 'dropdown'],
        data() {
          return {
              show : false,
              collapsing : false
          }
        },
        methods: {
            toggle() {
                this.show = !this.show
            }
        },
        computed : {
            eventName : function() {
                return (this.prefix ? this.prefix : 'event') + '_' + this.event.id + '_' + this.type.replace(/\\/g, '_');
            },
            statusClass: function() {
                if(typeof this.status == 'undefined') {
                    if(this.event.hasOwnProperty('failed') && this.event.hasOwnProperty('completed') && this.event.hasOwnProperty('started')) {
                        if(!this.event.failed && ! this.event.completed && !this.event.started) {
                            return 'events--item-status-neutral';
                        } else if(this.event.failed) {
                            return 'events--item-status-error';
                        } else if(this.event.completed) {
                            return 'events--item-status-success';
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