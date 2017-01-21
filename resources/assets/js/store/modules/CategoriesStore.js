export default {
    state: {
        category: null,
        categories: null
    },
    actions: {
        getCategory: ({ commit }, category) => {
            Vue.http.get(Vue.action('CategoriesController@show', { category: category })).then((response) => {
                commit('SET_CATEGORY', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        getCategories: ({ commit }) => {
            Vue.http.get(Vue.action('CategoriesController@index')).then((response) => {
                commit('SET_CATEGORIES', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        updateCategory: ({ commit }, data) => {
            Vue.http.put(Vue.action('CategoriesController@update', { category: data.category }), data).then((response) => {
                commit('SET_CATEGORY', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteCategory: ({ commit }, category) => {
            Vue.http.delete(Vue.action('CategoriesController@destroy', { category: category }), data).then(() => {
                commit('REMOVE_CATEGORY', category)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        SET_CATEGORY: (state, category) => {
            state.category = category
        },
        SET_CATEGORIES: (state, categories) => {
            state.categories = categories
        },
        REMOVE_CATEGORY: (state, categoryId) => {
            Vue.set(state, 'categories', _.reject(state.categories, { id: categoryId }))
        }
    }
}
