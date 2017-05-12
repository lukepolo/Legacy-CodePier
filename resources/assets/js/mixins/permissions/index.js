export const teamsEnabled = () => {
    return Laravel.teams
}
export const isAdmin = () => {
    return this.$store.state.userStore.user.role === 'admin'
}