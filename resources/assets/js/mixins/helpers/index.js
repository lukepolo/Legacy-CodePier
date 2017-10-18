import { createForm, getFormData } from "./form";

import { getPile } from "./pile";

import { getSite, hasSites, workFlowCompleted } from "./site";

import { back, action } from "./routes";

import { getServer, serverHasFeature } from "./server";
{ action}
import { isTag, hasClass } from "./elements";

import { now, diff, parseDate } from "./date-time";

import { getBytesFromString } from "./file-size";

import { local } from "./environment";

import { isAdmin, isSubscribed, teamsEnabled } from "./permissions";

import { showError, showSuccess, handleApiError } from "./notifications";

import { isCommandRunning } from "./server-command";

import { getRepositoryProvider } from "./repository-provider";

Vue.mixin({
  methods: {
    now,
    back,
    diff,
    isTag,
    action,
    getPile,
    getSite,
    hasClass,
    getServer,
    showError,
    parseDate,
    createForm,
    showSuccess,
    getFormData,
    handleApiError,
    isCommandRunning,
    serverHasFeature,
    getBytesFromString,
    getRepositoryProvider
  },
  computed: {
    local,
    isAdmin,
    hasSites,
    teamsEnabled,
    isSubscribed,
    workFlowCompleted
  }
});
