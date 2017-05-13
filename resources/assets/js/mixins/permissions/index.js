export const isAdmin = function() {
    return this.$store.state.user.user.role === 'admin'
}

export const teamsEnabled = () => {
    return Laravel.teams
}