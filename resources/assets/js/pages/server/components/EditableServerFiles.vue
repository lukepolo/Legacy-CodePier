<template>
    <section>
        <div v-for="(files, service) in editable_files">
            <template v-for="file in files">
                {{ sectionTitle(service) }}
                <server-file :server="server" :file="file"></server-file>
            </template>
        </div>
    </section>
</template>

<script>
    import ServerFile from './../../../components/ServerFile.vue';
    export default {
        components: {
            ServerFile
        },
        props: ['server'],
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData: function () {
                serverStore.dispatch('getEditableServerFiles', this.server);
            },
            sectionTitle: function (section) {
                return section.replace('install', '');
            }
        },
        computed: {
            editable_files: () => {
                return serverStore.state.editable_server_files;
            }
        }
    }
</script>