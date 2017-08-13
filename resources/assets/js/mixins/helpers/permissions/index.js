export const isAdmin = function() {
  return this.$store.state.user.user.role === "admin";
};

export const teamsEnabled = () => {
  return false; // Laravel.teams
};

export const isSubscribed = function() {
  return this.$store.state.user.user.is_subscribed;
};
