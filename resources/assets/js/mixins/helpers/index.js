import {
    createForm,
    getFormData
} from './form'

import {
    getPile
} from './pile'

import {
    getSite,
    hasSites,
    workFlowCompleted
} from './site'

import {
    back,
    action
} from './routes'

import {
    getServer,
    serverHasFeature
} from './server'

import {
    isTag,
    hasClass
} from './elements'

import {
    now,
    timeAgo,
    parseDate,
    dateHumanize
} from './date-time'

import {
    getBytesFromString
} from './file-size'

import {
    local
} from './environment'

import {
    isAdmin,
    teamsEnabled
} from './permissions'

import {
    showError,
    showSuccess,
    handleApiError
} from './notifications'

import {
    isCommandRunning
} from './server-command'

import {
    getRepositoryProvider
} from './repository-provider'

Vue.mixin({
    methods: {
        now,
        back,
        local,
        isTag,
        action,
        getPile,
        getSite,
        timeAgo,
        hasClass,
        getServer,
        showError,
        parseDate,
        createForm,
        showSuccess,
        getFormData,
        dateHumanize,
        handleApiError,
        isCommandRunning,
        serverHasFeature,
        getBytesFromString,
        getRepositoryProvider
    },
    computed: {
        isAdmin,
        hasSites,
        teamsEnabled,
        workFlowCompleted
    }
})
