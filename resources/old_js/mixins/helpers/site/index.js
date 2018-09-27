export const workFlowCompleted = function() {
  let site = this.$store.state.user_sites.site;

  if (site && site.repository && site.workflow) {
    let workflows = _.filter(
      _.sortBy(
        _.map(site.workflow, function(flow, step) {
          if (_.isObject(flow)) {
            flow.step = step;
          }
          return flow;
        }),
        "order",
      ),
      function(workflow) {
        return _.isObject(workflow);
      },
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
