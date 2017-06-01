export const setAll = (state, { response }) => {
    state.cron_jobs = response
}

export const add = (state, { response }) => {
    state.cron_jobs.push(response)
}

export const remove = (state, { requestData }) => {
    Vue.set(state, 'cron_jobs', _.reject(state.cron_jobs, { id: requestData.cron_job }))
}
