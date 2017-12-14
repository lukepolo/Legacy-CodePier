export const isAdmin = function() {
  return this.$store.state.user.user.role === "admin";
};

export const teamsEnabled = () => {
  return false;
};

export const isSubscribed = function() {
    return this.$store.state.user.user.is_subscribed;
};

export const apiEnabled = function() {
  if(this.isSubscribed) {
    return this.$store.state.user.user.subscription_plan.includes('captain')
  }
  return false;
};

export const siteActionsEnabled = function() {
  if(this.isSubscribed) {
    return true;
  }
  return this.$store.state.user_sites.sites.length <= 1;
};

export const serverTypesEnabled = function() {
  return this.isSubscribed;
};

export const serverActionsEnabled = function() {

  let numberOfServers = this.$store.state.user_servers.servers.length;

  if(!this.isSubscribed) {
    return numberOfServers <= 1;
  }

  if(this.$store.state.user.user.subscription_plan.includes('firstmate')) {
    return numberOfServers <= 30;
  }

  return true;
};

export const siteCreateEnabled = function() {
  if(this.isSubscribed) {
    return true;
  }
  return this.$store.state.user_sites.sites.length < 1;
}

export const serverCreateEnabled = function() {
  let numberOfServers = this.$store.state.user_servers.servers.length;

  if(!this.isSubscribed) {
    return numberOfServers < 1;
  }

  if(this.$store.state.user.user.subscription_plan.includes('firstmate')) {
    return numberOfServers < 30;
  }

  return true;
}