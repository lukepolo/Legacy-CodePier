export const setAll = (state, {response}) => {
    state.language_settings = response
}

export const setAvailableLanguageSettings = (state, {response}) => {
    state.available_language_settings = response
}

export const remove = (state, {requestData}) => {
    Vue.set(state, 'cron_jobs', _.reject(state.cron_jobs, { id: requestData.cron_job }))
}