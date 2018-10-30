<template>
    <div class="component-wrap">

        <!-- filers -->
        <v-card>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-text-field prepend-icon="search" box label="Filter By Name or Extension" v-model="filters.name"></v-text-field>
                    </v-flex>
                    <v-flex xs12>
                        Show Only:
                    </v-flex>
                    <v-flex xs4 md2 v-for="(group,i) in filters.fileGroupsHolder" :key="i">
                        <v-checkbox v-bind:label="group.name" v-model="filters.fileGroupId[group.id]"></v-checkbox>
                    </v-flex>
                </v-layout>
            </v-card-text>
        </v-card>

        <!-- groups table -->
        <v-data-table
                v-bind:headers="headers"
                v-bind:pagination.sync="pagination"
                :items="items"
                :total-items="totalItems"
                class="elevation-1">
            <template slot="headerCell" slot-scope="props">
                <span v-if="props.header.value=='thumb'">
                    <v-icon>panorama</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='group'">
                    <v-icon>folder</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='created_at'">
                    <v-icon>date_range</v-icon> {{ props.header.text }}
                </span>
                <span v-else>{{ props.header.text }}</span>
            </template>
            <template slot="items" slot-scope="props">
                <td class="wask_td_action">
                    <v-btn @click="showDialog('file_show',props.item)" icon small>
                        <v-icon dark class="blue--text">search</v-icon>
                    </v-btn>
                    <v-btn @click="trash(props.item)" icon small>
                        <v-icon class="red--text">delete</v-icon>
                    </v-btn>
                </td>
                <td>
                    <v-avatar
                            tile
                            :size="'50px'"
                            class="grey lighten-4">
                        <img :src="getFullUrl(props.item,50,'fit')"/>
                    </v-avatar>
                </td>
                <td>{{ props.item.name }}</td>
                <td>{{ $appFormatters.formatByteToMB(props.item.size).toString() + ' MB' }}</td>
                <td>{{ props.item.group.name }}</td>
                <td>{{ $appFormatters.formatDate(props.item.created_at) }}</td>
            </template>
        </v-data-table>
        <!-- /groups table -->

        <!-- view file dialog -->
        <v-dialog v-model="dialogs.view.show" fullscreen :laze="false" transition="dialog-bottom-transition" :overlay=false>
            <v-card>
                <v-toolbar class="primary">
                    <v-btn icon @click.native="dialogs.view.show = false" dark>
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title class="white--text">{{dialogs.view.file.name}}</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat @click.native="downloadFile(dialogs.view.file)">
                            Download
                            <v-icon right dark>file_download</v-icon></v-btn>
                    </v-toolbar-items>
                    <v-toolbar-items>
                        <v-btn dark flat @click.native="trash(dialogs.view.file)">
                            Delete
                            <v-icon right dark>delete</v-icon></v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                    <div class="file_view_popup">
                        <div class="file_view_popup_link">
                            <v-text-field flat disabled :value="getFullUrl(dialogs.view.file)"></v-text-field>
                        </div>
                        <img :src="getFullUrl(dialogs.view.file)"/>
                    </div>
                </v-card-text>
            </v-card>
        </v-dialog>

    </div>
</template>

<script>
    export default {
        components: {},
        data() {
            return {
                headers: [
                    { text: 'Action', value: false, align: 'left', sortable: false },
                    { text: 'Thumb', value: 'thumb', align: 'left', sortable: false },
                    { text: 'Name', value: 'name', align: 'left', sortable: false },
                    { text: 'Size', value: 'size', align: 'left', sortable: false },
                    { text: 'Found In', value: 'group', align: 'left', sortable: false },
                    { text: 'Date Created', value: 'created_at', align: 'left', sortable: false },
                ],
                items: [],
                totalItems: 0,
                pagination: {
                    rowsPerPage: 10
                },

                filters: {
                    name: '',
                    selectedGroupIds: '',
                    fileGroupId: [],
                    fileGroupsHolder: []
                },

                dialogs: {
                    view: {
                        file: {},
                        show: false
                    },
                }
            }
        },
        mounted() {
            console.log('pages.files.components.FileLists.vue');

            const self = this;

            self.$eventBus.$on(['FILE_DELETED','FILE_UPLOADED'], function () {
                self.loadFiles(()=>{});
            });
        },
        watch: {
            'filters.fileGroupId':_.debounce(function(v) {

                let selected = [];

                _.each(v,(v,k)=>{
                    if(v) selected.push(k);
                });

                this.filters.selectedGroupIds = selected.join(',');
            },500),
            'filters.selectedGroupIds'(v) {
                this.loadFiles(()=>{});
            },
            'filters.name':_.debounce(function(v) {
                this.loadFiles(()=>{});
            },500),
            'pagination.page':function(){
                this.loadFiles(()=>{});
            },
            'pagination.rowsPerPage':function(){
                this.loadFiles(()=>{});
            },
        },
        methods: {
            getFullUrl(file, width, action) {

                let w = width || 4000;
                let act = action || 'resize';

                return LSK_APP.APP_URL +`/files/`+file.id+`/preview?w=`+w+`&action=`+act;
            },
            downloadFile(file) {
                window.open(LSK_APP.APP_URL + '/files/'+file.id+'/download?file_token='+file.file_token);
            },
            showDialog(dialog, data) {

                const self = this;

                switch (dialog){
                    case 'file_show':
                        self.dialogs.view.file = data;
                        setTimeout(()=>{
                            self.dialogs.view.show = true;
                        },500);
                        break;
                }
            },
            trash(file) {
                const self = this;

                self.$store.commit('showDialog',{
                    type: "confirm",
                    title: "Confirm Deletion",
                    message: "Are you sure you want to delete this file?",
                    okCb: ()=>{

                        axios.delete('/admin/files/' + file.id).then(function(response) {

                            self.$store.commit('showSnackbar',{
                                message: response.data.message,
                                color: 'success',
                                duration: 3000
                            });

                            self.$eventBus.$emit('FILE_DELETED');

                            // maybe the action took place from view file
                            // lets close it.
                            self.dialogs.view.show = false;

                        }).catch(function (error) {
                            if (error.response) {
                                self.$store.commit('showSnackbar',{
                                    message: error.response.data.message,
                                    color: 'error',
                                    duration: 3000
                                });
                            } else if (error.request) {
                                console.log(error.request);
                            } else {
                                console.log('Error', error.message);
                            }
                        });
                    },
                    cancelCb: ()=>{
                        console.log("CANCEL");
                    }
                });
            },
            loadFileGroups(cb) {

                const self = this;

                let params = {
                    paginate: 'no'
                };

                axios.get('/admin/file-groups',{params: params}).then(function(response) {
                    self.filters.fileGroupsHolder = response.data.data;
                    cb();
                });
            },
            loadFiles(cb) {

                const self = this;

                let params = {
                    name: self.filters.name,
                    file_group_id: self.filters.selectedGroupIds,
                    page: self.pagination.page,
                    per_page: self.pagination.rowsPerPage
                };

                axios.get('/admin/files',{params: params}).then(function(response) {
                    self.items = response.data.data.data;
                    self.totalItems = response.data.data.total;
                    self.pagination.totalItems = response.data.data.total;
                    (cb || Function)();
                });
            }
        }
    }
</script>

<style scoped>
    .file_view_popup {
        min-width: 500px;
        text-align: center;
    }
</style>