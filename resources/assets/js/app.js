var Vue = require('vue');

import {now, parseDate, dateHumanize} from './services/date';
import {createLink} from './services/routes';

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

        /*
         |--------------------------------------------------------------------------
         | Helpers
         |--------------------------------------------------------------------------
         |
         */

        action: function (action, parameters) {
            return createLink(action, parameters);
        }
    }
});