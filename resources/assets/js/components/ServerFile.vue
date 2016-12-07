<template>
    <form @submit.prevent="saveFile()">
        {{ file }}
        <div v-file-editor class="editor"></div>
        <button type="submit">Update</button>
    </form>
</template>

<script>
    export default {
        props: ['server', 'file'],
        data() {
            return {
                content: null
            }
        },
        watch: {
            'content'() {
                ace.edit($(this.$el).find('.editor')[0]).setValue(this.content);
                ace.edit($('.editor')[0]).clearSelection(1);
            }
        },
        mounted() {
            Vue.http.post(laroute.action('Server\ServerController@getFile', {
                server: this.server,
            }), {
                file: this.file,
            }).then((response) => {
                this.content = response.data;
            }, (errors) => {
                app.showError(error);
            });
        },
        methods: {
            saveFile() {
                this.$store.dispatch('saveServerFile', {
                    file: this.file,
                    server: this.server,
                    content: this.getContent(),
                });
            },
            getContent() {
                return ace.edit($(this.$el).find('.editor')[0]).getValue();
            }
        }
    }
</script>