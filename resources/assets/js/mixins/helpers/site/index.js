export const hasSites = function() {
  return this.$store.state.user_sites.sites.length > 0;
};

export const getSite = function(siteId, attribute) {
  const site = _.find(this.$store.state.user_sites.sites, {
    id: parseInt(siteId),
  });
  if (site) {
    if (attribute) {
      return site[attribute];
    }
    return site;
  }
  return {};
};

export const workFlowCompleted = function() {
  let site = this.$store.state.user_sites.site;

  if (site && site.repository && site.workflow) {
    let workflows = _.sortBy(
      _.map(site.workflow, function(flow, step) {
        flow.step = step;
        return flow;
      }),
      'order',
    );

    let currentWorkflow = _.find(workflows, function(flow) {
      return flow.completed === false;
    });

    if (currentWorkflow) {
      return currentWorkflow.step;
    }

    return true;
  }

  return false;
};
