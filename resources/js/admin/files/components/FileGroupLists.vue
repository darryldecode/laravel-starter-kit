<template>
    <div class="component-wrap">

        <!-- search -->
        <v-card>
            <div class="d-flex flex-row">
                <div class="flex-grow-1">
                    <v-text-field prepend-icon="search" label="Filter By Name" v-model="filters.name"></v-text-field>
                </div>
                <div class="flex-grow-1 text-right">
                    <v-btn @click="showDialog('file_group_add')" dark class="primary lighten-1">
                        New File Group
                        <v-icon right>mdi-add</v-icon>
                    </v-btn>
                </div>
            </div>
        </v-card>
        <!-- /search -->

        <!-- groups table -->
        <v-data-table
                v-bind:headers="headers"
                :options.sync="pagination"
                :items="items"
                :server-items-length="totalItems"
                class="elevation-1">
            <template v-slot:body="{items}">
                <tbody>
                <tr v-for="item in items" :key="item.id">
                    <td>
                        <v-btn @click="showDialog('file_group_edit',item)" icon small>
                            <v-icon class="blue--text">edit</v-icon>
                        </v-btn>
                        <v-btn @click="trash(props.item)" icon small>
                            <v-icon class="red--text">delete</v-icon>
                        </v-btn>
                    </td>
                    <td>{{ item.name }}</td>
                    <td>{{ item.description }}</td>
                    <td>{{ item.file_count }}</td>
                    <td>{{ $appFormatters.formatDate(item.created_at) }}</td>
                </tr>
                </tbody>
            </template>
        </v-data-table>

        <!-- add file group -->
        <v-dialog v-model="dialogs.add.show" fullscreen transition="dialog-bottom-transition" :overlay=false>
            <v-card>
                <v-toolbar class="primary">
                    <v-btn icon @click.native="dialogs.add.show = false">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Create New File Group</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn text @click.native="dialogs.add.show = false">Done</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                    <file-group-add></file-group-add>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- edit file group -->
        <v-dialog v-model="dialogs.edit.show" fullscreen :laze="false" transition="dialog-bottom-transition" :overlay=false>
            <v-card>
                <v-toolbar class="primary">
                    <v-btn icon @click.native="dialogs.edit.show = false">
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Edit File Group</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn text @click.native="dialogs.edit.show = false">Done</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                    <file-group-edit :propFileGroupId="dialogs.edit.fileGroup.id"></file-group-edit>
                </v-card-text>
            </v-card>
        </v-dialog>

    </div>
</template>

<script>
    import FileGroupAdd from './FileGroupAdd.vue';
    import FileGroupEdit from './FileGroupEdit.vue';
    export default {
        components: {
            FileGroupAdd,
            FileGroupEdit
        },
        data() {
            return {
                headers: [
                    { text: 'Action', value: false, align: 'left', sortable: false },
                    { text: 'Group Name', value: 'name', align: 'left', sortable: false },
                    { text: 'Description', value: 'description', align: 'left', sortable: false },
                    { text: 'Total Files', value: 'file_count', align: 'left', sortable: false },
                    { text: 'Date Created', value: 'created_at', align: 'left', sortable: false },
                ],
                items: [],
                totalItems: 0,
                pagination: {
                    rowsPerPage: 10
                },

                filters: {
                    name: '',
                },

                dialogs: {
                    edit: {
                        fileGroup: {},
                        show: false
                    },
                    add: {
                        show: false
                    }
                }
            }
        },
        mounted() {
            console.log('pages.files.components.FileGroupLists.vue');

            const self = this;

            self.$eventBus.$on(['FILE_GROUP_ADDED','FILE_GROUP_UPDATED','FILE_GROUP_DELETED'],()=>{
                self.loadFileGroups(()=>{});
            });
        },
        watch: {
            'filters.name':_.debounce(function(v) {
                this.loadFileGroups(()=>{});
            },500),
            'pagination.page':function(){
                this.loadFileGroups(()=>{});
            },
            'pagination.rowsPerPage':function(){
                this.loadFileGroups(()=>{});
            },
        },
        methods: {
            trash(group) {
                const self = this;

                self.$store.commit('showDialog',{
                    type: "confirm",
                    title: "Confirm Deletion",
                    message: "Are you sure you want to delete this file group?",
                    okCb: ()=>{

                        axios.delete('/admin/file-groups/' + group.id).then(function(response) {

                            self.$store.commit('showSnackbar',{
                                message: response.data.message,
                                color: 'success',
                                duration: 3000
                            });

                            self.$eventBus.$emit('FILE_GROUP_DELETED');

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
            showDialog(dialog, data) {

                const self = this;

                switch (dialog){
                    case 'file_group_edit':
                        self.dialogs.edit.fileGroup = data;
                        setTimeout(()=>{
                            self.dialogs.edit.show = true;
                        },500);
                        break;
                    case 'file_group_add':
                        setTimeout(()=>{
                            self.dialogs.add.show = true;
                        },500);
                        break;
                }
            },
            loadFileGroups(cb) {

                const self = this;

                let params = {
                    name: self.filters.name,
                    page: self.pagination.page,
                    per_page: self.pagination.rowsPerPage
                };

                axios.get('/admin/file-groups',{params: params}).then(function(response) {
                    self.items = response.data.data.data;
                    self.totalItems = response.data.data.total;
                    self.pagination.totalItems = response.data.data.total;
                    (cb || Function)();
                });
            }
        }
    }
</script>