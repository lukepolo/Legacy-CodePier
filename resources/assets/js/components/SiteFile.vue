<template>
    <form @submit.prevent="saveFile()">
        {{ file }}
        <div v-file-editor class="editor"></div>
        <div class="form-group" v-for="server in servers" v-if="servers">
            <input type="checkbox" :value="server.id" v-model="selected_servers" name="selected_servers[]"> {{ server.ip }}
        </div>
        <button type="submit">Update</button>
    </form>
</template>

<script>
    export default {
        props: ['site', 'servers', 'file'],
        data() {
            return {
                content: null,
                file_model: null,
                selected_servers : _.map(this.site.servers, 'id'),
            }
        },
        watch: {
            'content': function () {
                ace.edit($(this.$el).find('.editor')[0]).setValue(this.content);
                ace.edit($('.editor')[0]).clearSelection(1);
            }
        },
        mounted: function () {
            Vue.http.post(laroute.action('Site\SiteFileController@find', {
                site: this.site.id,
            }), {
                file: this.file,
            }).then((response) => {
                this.file_model = response.json();
                this.content = this.file_model.unencrypted_content;
            }, (errors) => {
                alert(error);
            });
        },
        methods: {
            saveFile() {
                if(this.file_model) {
                    siteStore.dispatch('updateSiteFile', {
                        file: this.file,
                        site: this.site.id,
                        content : this.getContent(),
                        file_id : this.file_model.id,
                        servers : this.selected_servers,

                    });
                } else {
                    siteStore.dispatch('saveSiteFile', {
                        file: this.file,
                        site: this.site.id,
                        content : this.getContent(),
                        servers : this.selected_servers,
                    });
                }

            },
            getContent() {
                return ace.edit($(this.$el).find('.editor')[0]).getValue();
            }
        }
    }
</script>