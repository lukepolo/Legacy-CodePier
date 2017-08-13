import Vue from "vue";
import { default as routes } from "../app/routes.js";

window.VueRouter = require("vue-router/dist/vue-router.common");

Vue.use(VueRouter);

const router = new VueRouter({
  mode: "history",
  routes: routes
});

router.beforeResolve((to, from, next) => {
    if(!store.state.user.user.is_subscribed) {
        if(to.name !== 'subscription') {
            next({
                name : 'subscription'
            })
        } else {
          next()
        }
    } else {
        next()
    }
})

export default router;
