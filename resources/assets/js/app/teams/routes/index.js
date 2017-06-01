import {
    Teams,
    TeamMembers
} from '../pages'

import {
} from '../components'

export default [
    { path: '/my/teams', name: 'teams', component: Teams },
    { path: '/my/team/:team_id/members', name: 'team_members', component: TeamMembers }
]
