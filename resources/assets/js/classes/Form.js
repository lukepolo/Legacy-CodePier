import Request from './Request'

class Form extends Request {

    /**
     * Reset the form fields.
     */
    reset() {

        for (let field in this.originalData) {
            this[field] = this.originalData[field]
        }

        this.errors.clear()
    }

    /**
     * Handle a successful form submission.
     *
     * @param {object} data
     */
    onSuccess(data) {
        if(this.resetData) {
            this.reset()
        }
        this.errors.clear()
    }

    diff() {
        return _.reduce(this.data(), (result, value, key) => {
            return _.isEqual(value, this.originalData[key]) ?
                result : result.concat(key);
        }, []);
    }
}

export default Form