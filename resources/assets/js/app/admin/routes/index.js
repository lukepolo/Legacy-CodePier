import {
    BuoyForm,
    AdminArea,
    Categories,
    CategoryForm,
} from '../components'

export default [
    {
        path: '/admin', component: AdminArea,
        children: [
            {
                path: 'categories',
                name: 'categories',
                components: {
                    default: Categories
                }
            },
            {
                path: 'categories/create',
                name: 'category_create',
                components: {
                    default: CategoryForm
                }
            },
            {
                path: 'categories/edit/:category_id',
                name: 'category_edit',
                components: {
                    default: CategoryForm
                }
            },
            {
                path: 'buoys/edit/:buoy_id',
                name: 'buoy_edit',
                components: {
                    default: BuoyForm
                }
            }
        ]
    },
]