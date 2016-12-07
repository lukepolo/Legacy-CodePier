<template>
    <form @submit.prevent="saveFile()">
        {{ file }}
        <div v-file-editor class="editor"></div>
        <server-selector :servers="servers" :param="selected_servers"></server-selector>
        <div class="btn-footer">
            <button class="btn btn-primary" type="submit">Update File</button>
        </div>
    </form>
</template>

<script>
    import ServerSelector from './ServerSelector.vue';
    export default {
        props: ['site', 'servers', 'file'],
        components: {
            ServerSelector
        },
        created() {
            this.fetchData();
        },
        data() {
            return {
                content: null,
                file_model: null,
                selected_servers: _.map(this.site.servers, 'id'),
            }
        },
        watch: {
            'content'() {
                ace.edit($(this.$el).find('.editor')[0]).setValue(this.content);
                ace.edit($('.editor')[0]).clearSelection(1);
            },
            watch: {
                '$route': 'fetchData'
            }
        },
        methods: {
            fetchData() {
                Vue.http.post(laroute.action('Site\SiteFileController@find', {
                    site: this.site.id,
                }), {
                    file: this.file,
                }).then((response) => {
                    this.file_model = response.data;
                    this.content = this.file_model.unencrypted_content;
                }, (errors) => {
                    app.showError(error);
                });
            },
            saveFile() {
                if (this.file_model) {
                    this.$store.dispatch('updateSiteFile', {
                        file: this.file,
                        site: this.site.id,
                        content: this.getContent(),
                        file_id: this.file_model.id,
                        servers: this.selected_servers,

                    });
                } else {
                    this.$store.dispatch('saveSiteFile', {
                        file: this.file,
                        site: this.site.id,
                        content: this.getContent(),
                        servers: this.selected_servers,
                    });
                }

            },
            getContent() {
                return ace.edit($(this.$el).find('.editor')[0]).getValue();
            }
        }
    }
</script>