var Vue = require('vue');

Vue.mixin({
    methods : {
        now : function() {
            return moment();
        },
        parseDate : function(date, timezone) {
            if (timezone) {
                return moment(date).tz(timezone);
            }
            return moment(date);
        },
        dateHumanize : function(date, timezone) {
            return moment(date).tz(timezone).fromNow();
        },
        action: function (action, parameters) {
            return laroute.action(action, parameters);
        }
    }
});

vue = new Vue({
    el: '#app-layout',

    components: {

    },

    computed: {

    },

    data: {
        servers : servers
    },

    events: {

    },

    methods: {

    }
});