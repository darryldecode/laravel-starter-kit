<template>
    <div class="page_wrap_vue">
        <v-tabs color="primary" dark v-model="active">

            <v-tab href="#files" ripple>
                Files
            </v-tab>
            <v-tab href="#manage-groups" ripple>
                Manage File Groups
            </v-tab>
            <v-tab href="#upload" ripple>
                Upload
            </v-tab>

            <v-tab-item id="files">
                <v-card flat>
                    <v-card-text>
                        <file-lists></file-lists>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item id="manage-groups">
                <v-card flat>
                    <v-card-text>
                        <file-group-lists></file-group-lists>
                    </v-card-text>
                </v-card>
            </v-tab-item>
            <v-tab-item id="upload">
                <v-card flat>
                    <v-card-text>
                        <file-upload></file-upload>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs>
    </div>
</template>

<script>
    import FileGroupLists from './components/FileGroupLists.vue';
    import FileUpload from './components/FileUpload.vue';
    import FileLists from './components/FileLists.vue';
    export default {
        components: {
            FileUpload,
            FileGroupLists,
            FileLists
        },
        mounted() {
            console.log('pages.FileManager.vue');

            const self = this;
        },
        data() {
            return {
                active: ''
            }
        },
        watch: {
            active(v) {
                console.log('active tab: ' + v);
            }
        },
        methods: {
        }
    }
</script>

<style scoped="">
    .finder_wrap {
        padding: 0 20px;
    }
</style>