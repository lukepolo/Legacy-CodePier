<template>
    <section>
        <div>
            <h1>{{ database }}</h1>

            <div class="jcf-form-wrap">
                <form @submit.prevent="createSchema(database)" class="floating-labels">
                    <h3>Schemas :</h3>
                    <div class="jcf-input-group">
                        <input type="text" name="command" v-model="form.command">
                        <label for="command">
                            <span class="float-label">Command</span>
                        </label>
                    </div>

                    <div class="btn-footer">
                        <button class="btn btn-primary" type="submit">Create Queue Worker</button>
                    </div>

                </form>
            </div>


            <h3>Users</h3>
            Name
            User / Password (options)

            can access databases above
        </div>
    </section>
</template>

<script>
    export default {
        props : ['database']
    }
</script>