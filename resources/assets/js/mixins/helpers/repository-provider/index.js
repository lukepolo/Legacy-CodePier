export const getRepositoryProvider = function(providerId, attribute) {
  const provider = _.find(this.$store.state.repository_providers.providers, {
    id: providerId
  });
  if (provider) {
    if (attribute) {
      return provider[attribute];
    }
    return provider;
  }
  return {};
};
