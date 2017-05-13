import {
    BuoyMarketPlace,
} from '../pages'

import {
    BuoyArea,
    BuoyInstall,
} from '../components'

export default [
    {
        path: '/buoys', component: BuoyArea,
        children: [
            {
                path: '/',
                name: 'buoy_market_place',
                components: {
                    default: BuoyMarketPlace,
                    right: BuoyInstall
                }
            }
        ]
    },
]