export const isAdmin = function() {
    return this.$store.state.userStore.user.role === 'admin'
}

export const teamsEnabled = () => {
    return Laravel.teams
}