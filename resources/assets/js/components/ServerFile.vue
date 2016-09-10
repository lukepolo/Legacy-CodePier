<template>
    <form @submit.prevent="updateFile(server, file)">
        {{ file }}
        <div v-file-editor="{server:server, file:file}" class="editor"></div>
        <textarea style="display: none" :name="server+file"></textarea>
        <button type="submit">Update</button>
    </form>
</template>

<script>
    export default {
        props: ['server', 'file'],
        methods: {
            // TODO - better way of getting contents of file, annoying that a lot of the methods i tried didnt work
            updateFile(server, file) {

                var content = $('textarea[name="'+ server + file +'"]').val();

                console.log(content);
                Vue.http.post(laroute.action('Server\ServerController@saveFile', {
                    file: file,
                    server: server,
                    content : escape(content),
                })).then((response) => {

                }, (errors) => {
                    alert(error);
                });
            }
        }
    }
</script>