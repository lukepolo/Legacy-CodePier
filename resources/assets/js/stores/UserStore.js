import Vue from "vue/dist/vue";
import Vuex from "vuex";
import {action} from "./helpers";

Vue.use(Vuex);

const userStore = new Vuex.Store({
    state: {
        user: user
    }
});

export default userStore