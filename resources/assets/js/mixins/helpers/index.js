import { createForm, getFormData } from "./form";

import { getPile } from "./pile";

import { getSite, hasSites, workFlowCompleted } from "./site";

import { action } from "./routes";

import { getServer, serverHasFeature } from "./server";

import { isTag, hasClass } from "./elements";

import { now, diff, parseDate } from "./date-time";

import { megaBytesToHumanReadable } from "./file-size";

import { local } from "./environment";

import {
  isAdmin,
  isSubscribed,
  teamsEnabled,
  siteActionsEnabled,
  serverActionsEnabled,
  serverTypesEnabled,
  apiEnabled,
  siteCreateEnabled,
  serverCreateEnabled,
} from "./permissions";

import { showError, showSuccess, handleApiError } from "./notifications";

import { isCommandRunning } from "./server-command";

Vue.mixin({
  methods: {
    now,
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
    megaBytesToHumanReadable,
  },
  computed: {
    local,
    isAdmin,
    hasSites,
    workFlowCompleted,

    // Security of features
    apiEnabled,
    isSubscribed,
    teamsEnabled,
    siteCreateEnabled,
    siteActionsEnabled,
    serverTypesEnabled,
    serverCreateEnabled,
    serverActionsEnabled,
  },
});
