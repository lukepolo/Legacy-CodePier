<template>
    <form @submit.prevent="installBlackfire">
        server id
        <input type="text" name="server_id">
        server token
        <input type="text" name="server_token">
        <button type="submit">Install Blackfire</button>
    </form>
</template>

<script>
    export default {
        props : ['server'],
        methods : {
            installBlackfire : function() {
                alert('install blackfire on ' + this.server.id);
            }
        }
    }
</script>