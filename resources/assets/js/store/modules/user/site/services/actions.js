export const restart = ({}, site) => {
    Vue.request().post(
        Vue.action('Site\SiteController@restartSite', { site: site })
    ).then(() => {
        app.showSuccess('You have restarted your site')
    })
}
export const restartWebServices = ({}, site) => {
    Vue.request().post(
        Vue.action('Site\SiteController@restartWebServices', { site: site })
    ).then(() => {
        app.showSuccess('You have restarted your web services')
    })
}

export const restartDatabases = ({}, site) => {
    Vue.request().post(
        Vue.action('Site\SiteController@restartDatabases', { site: site })
    ).then(() => {
        app.showSuccess('You have restarted your databases')
    })
}

export const restartWorkers = ({}, site) => {
    Vue.request().post(
        Vue.action('Site\SiteController@restartWorkerServices', {site: site})
    ).then(() => {
        app.showSuccess('You have restarted your workers')
    })
}