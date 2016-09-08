<template>
    <section>
        <div v-for="(files, service) in editable_files">
            {{ sectionTitle(service) }}
            <p v-for="file in files">
                {{ file }}
            </p>
        </div>
    </section>
</template>

<script>
    export default {
        props: ['server'],
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData: function() {
                serverStore.dispatch('getEditableServerFiles', this.server);
            },
            sectionTitle : function(section) {
                return section.replace('install', '');
            }
        },
        computed: {
            editable_files : () => {
                return serverStore.state.editable_files;
            },
        }
    }
</script>