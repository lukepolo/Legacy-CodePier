import Vue from 'vue'
import Vuex from 'vuex'
import Form from './../classes/Form'
import Errors from './../classes/Errors'
import Request from './../classes/Request'
import { default as modules } from './modules'

Vue.use(Vuex)
Vue.Form = Form
Vue.Errors = Errors
Vue.Request = Request

Vue.request = (data) => {
    return new Request(data)
}

export default new Vuex.Store({
    modules: modules
})
