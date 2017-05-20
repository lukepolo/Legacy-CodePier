<template>
    <section>

        <div class="events--item-status" :class="statusClass" v-if="statusClass"></div>

        <a :class="{ collapsed : !show }" v-if="showDropDown" @click="toggle">
            <span class="icon-play"></span>
        </a>

        {{ title }}
        <transition name="collapse"

            v-on:before-enter="setup"
            v-on:enter="enter"
            v-on:after-enter="done"

            v-on:before-leave="setup"
            v-on:leave="leave"
            v-on:after-leave="done"

            v-bind:css="false"
        >
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
              collapsing : false,
              height : null,
              transitionSpeed : '.35'
          }
        },
        methods: {
            toggle() {
                this.show = !this.show
            },
            setup(el) {
                el.style.overflow = "hidden"
                el.style.position = "initial"
                el.style.transition = "all "+this.transitionSpeed+"s ease"
            },
            enter(el, done) {
                this.height =  el.offsetHeight
                el.style.height = 0

                setTimeout(() => {
                    el.style.height = this.height+'px'
                }, .00000001)

                setTimeout(function() {
                    done()
                }, this.transitionSpeed * 1000)
            },
            leave(el, done) {

                el.style.height = this.height+'px'

                setTimeout(() => {
                    el.style.height = 0
                }, .00000001)

                setTimeout(function() {
                    done()
                }, this.transitionSpeed * 1000)

            },
            done(el) {
                el.style.height = null
                el.style.overflow = null
                el.style.position = null
                el.style.transition = null
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