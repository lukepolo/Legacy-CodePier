import Errors from './Errors'

class Request {

    /**
     * Create a new Form instance.
     *
     * @param {object, FormData} data
     * @param {boolean} reset
     */
    constructor(data, reset) {

        this.resetData = reset ? reset : false

        if(data && !_.isObject(data)) {
            this['value'] = data
        } else {
            this.originalData = data
        }

        if(data instanceof FormData) {
            this.formData = data
        } else {
            for (let field in data) {
                this[field] = data[field]
            }
        }

        this.errors = new Errors()
    }

    /**
     * Fetch all relevant data for the form.
     */
    data() {

        if(this.formData) {
            return this.formData
        }

        let data = Object.assign({}, this)

        delete data.errors
        delete data.resetData
        delete data.originalData

        return data
    }

    /**
     * Send a GET request to the given URL.
     *
     * @param {string} url
     * @param {string|array} mutations
     * @param {array} config
     */
    get(url, mutations, config) {
        for (let value in config) {
            this[value] = config[value]
        }
        return this.submit('get', url, mutations, config)
    }

    /**
     * Send a POST request to the given URL.
     *
     * @param {string} url
     * @param {string|array} mutations
     * @param {array} config
     */
    post(url, mutations, config) {
        return this.submit('post', url, mutations, config)
    }

    /**
     * Send a PUT request to the given URL.
     *
     * @param {string} url
     * @param {string|array} mutations
     * @param {array} config
     */
    put(url, mutations, config) {
        return this.submit('put', url, mutations, config)
    }

    /**
     * Send a PATCH request to the given URL.
     *
     * @param {string} url
     * @param {string|array} mutations
     * @param {array} config
     */
    patch(url, mutations, config) {
        return this.submit('patch', url, mutations, config)
    }

    /**
     * Send a DELETE request to the given URL.
     *
     * @param {string} url
     * @param {string|array} mutations
     * @param {array} config
     */
    delete(url, mutations, config) {
        return this.submit('delete', url, mutations, config)
    }

    /**
     * Submit the form.
     *
     * @param {string} requestType
     * @param {string} url
     * @param {string|array} mutations
     * @param {array} config
     */
    submit(requestType, url, mutations, config) {
        return new Promise((resolve, reject) => {

            let data = this.formData ? this.formData :  this.data()

            axios[requestType](url, data, config)
                .then(response => {

                    if(response.config.responseType == 'arraybuffer') {

                        let a = document.createElement("a");
                        document.body.appendChild(a);

                        let blob = new Blob([response.data], { type : response.headers['content-type'] })

                        url = window.URL.createObjectURL(blob)

                        a.style = "display: none"
                        a.href = url
                        a.download = response.headers['content-disposition'].match(/"(.*?)"/)[1]
                        a.click();

                        window.URL.revokeObjectURL(url);

                    } else {
                        this.onSuccess(response.data)

                        if(!this.resetData) {
                            this.setOriginalData()
                        }

                        if(_.isString(mutations)) {
                            mutations = [mutations]
                        }

                        if(mutations && mutations.length) {
                            _.each(mutations, (mutation) => {
                                app.$store.commit(mutation, {
                                    response : response.data,
                                    requestData : this.data(),
                                })
                            })
                        }
                    }

                    resolve(response.data)

                })
                .catch(error => {
                    // TODO - handle errors here
                    // app.handleApiError(error)
                    if(error.response) {
                        this.onFail(error.response.data)
                        reject(error.response.data)
                    } else {
                        console.error(error)
                        reject(error.response)
                    }
                })
        })
    }

    /**
     * Handle a successful form submission.
     *
     * @param {object} data
     */
    onSuccess(data) {
        this.errors.clear()
    }

    /**
     * Handle a failed form submission.
     *
     * @param {object} errors
     */
    onFail(errors) {
        this.errors.record(errors)
    }

    /**
     * Sets the current data to the original data
     */
    setOriginalData() {
        this.originalData = this.data()
    }
}

export default Request