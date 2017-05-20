import {
    BittsForm,
    BittsMarketPlace
} from '../pages'

import {
    BittsArea,
    BittInstall
} from '../components'

export default [
    {
        path: '/bitts', component: BittsArea,
        children: [
            {
                path: '/',
                name: 'bitts_market_place',
                components: {
                    default: BittsMarketPlace,
                    right: BittInstall
                }
            },
            {
                path: 'create',
                name: 'bitt_create',
                components: {
                    default: BittsForm
                }
            },
            {
                path: ':bitt_id/edit',
                name: 'bitt_edit',
                components: {
                    default: BittsForm
                }
            }
        ]
    }
]
