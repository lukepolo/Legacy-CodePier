export const restartServers = ({}, site) => {
    Vue.request().post(
        Vue.action('Site\SiteController@restartServer', { site: site })
    ).then(() => {
        app.showSuccess('You have restarted your sites servers.')
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