<template>
    <div class="component-wrap">

        <!-- search -->
        <v-card dark>
            <v-container grid-list-md>
                <v-layout row wrap>
                    <v-flex xs12 sm12>
                        <v-btn @click="showDialog('permission_add')" class="blue lighten-1" dark>
                            New Permission
                            <v-icon right dark>add</v-icon>
                        </v-btn>
                    </v-flex>
                    <v-flex xs12>
                        <v-text-field prepend-icon="search" box dark label="Filter By Permission Title" v-model="filters.title"></v-text-field>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-card>
        <!-- /search -->

        <!-- groups table -->
        <v-data-table
                v-bind:headers="headers"
                :items="items"
                hide-actions
                class="elevation-1">
            <template slot="headerCell" scope="props">
                <span v-if="props.header.value=='permission'">
                    <v-icon>vpn_key</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='created_at'">
                    <v-icon>date_range</v-icon> {{ props.header.text }}
                </span>
                <span v-else>{{ props.header.text }}</span>
            </template>
            <template slot="items" scope="props">
                <td>
                    <v-btn @click="showDialog('permission_edit',props.item)" icon small>
                        <v-icon dark class="blue--text">edit</v-icon>
                    </v-btn>
                    <v-btn @click="trash(props.item)" icon small>
                        <v-icon dark class="red--text">delete</v-icon>
                    </v-btn>
                </td>
                <td>{{ props.item.title }}</td>
                <td>{{ props.item.permission }}</td>
                <td>{{ props.item.description }}</td>
                <td>{{ $appFormatters.formatDate(props.item.created_at) }}</td>
            </template>
        </v-data-table>
        <div class="text-xs-center">
            <v-pagination :length="totalPages" :total-visible="8" v-model="page" circle></v-pagination>
        </div>

        <!-- add permission -->
        <v-dialog v-model="dialogs.add.show" fullscreen transition="dialog-bottom-transition" :overlay=false>
            <v-card>
                <v-toolbar dark class="primary">
                    <v-btn icon @click.native="dialogs.add.show = false" dark>
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Create New Permission</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat @click.native="dialogs.add.show = false">Done</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>
                    <permission-form-add></permission-form-add>
                </v-card-text>
            </v-card>
        </v-dialog>

        <!-- edit permission -->
        <v-dialog v-model="dialogs.edit.show" fullscreen :laze="false" transition="dialog-bottom-transition" :overlay=false>
            <v-card>
                <v-toolbar dark class="primary">
                    <v-btn icon @click.native="dialogs.edit.show = false" dark>
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Edit Permission</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat @click.native="dialogs.edit.show = false">Done</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <v-card-text>

                </v-card-text>
            </v-card>
        </v-dialog>

    </div>
</template>

<script>
    import PermissionFormAdd from './PermissionFormAdd.vue';
    export default {
        components: {
            PermissionFormAdd
        },
        data () {
            return {
                headers: [
                    { text: 'Action', value: false, align: 'left', sortable: false },
                    { text: 'Title', value: 'name', align: 'left', sortable: false },
                    { text: 'Permission', value: 'permission', align: 'left', sortable: false },
                    { text: 'Description', value: 'description', align: 'left', sortable: false },
                    { text: 'Date Created', value: 'created_at', align: 'left', sortable: false },
                ],
                items: [],
                totalPages: 0,
                page: 1,

                filters: {
                    title: '',
                },

                dialogs: {
                    edit: {
                        group: {},
                        show: false
                    },
                    add: {
                        show: false
                    }
                }
            }
        },
        mounted() {
            const self = this;

            self.loadPermissions(()=>{});

            self.$eventBus.$on(['PERMISSION_ADDED','PERMISSION_UPDATED','PERMISSION_DELETED'],()=>{
                self.loadPermissions(()=>{});
            });
        },
        watch: {
            page(val) {
                const self = this;

                self.page = val;

                self.loadPermissions(()=>{});
            },
            'filters.title':_.debounce(function(){
                const self = this;
                self.loadPermissions(()=>{});
            },700),
        },
        methods: {
            trash(group) {
                const self = this;

                self.$store.commit('showDialog',{
                    type: "confirm",
                    title: "Confirm Deletion",
                    message: "Are you sure you want to delete this permission?",
                    okCb: ()=>{

                        axios.delete('/ajax/permissions/' + group.id).then(function(response) {

                            self.$store.commit('showSnackbar',{
                                message: response.data.message,
                                color: 'success',
                                duration: 3000
                            });

                            self.$eventBus.$emit('GROUP_DELETED');

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
                    case 'permission_edit':
                        self.dialogs.edit.group = data;
                        setTimeout(()=>{
                            self.dialogs.edit.show = true;
                        },500);
                        break;
                    case 'permission_add':
                        setTimeout(()=>{
                            self.dialogs.add.show = true;
                        },500);
                        break;
                }
            },
            loadPermissions(cb) {

                const self = this;

                let params = {
                    name: self.filters.name,
                    page: self.page
                };

                axios.get('/ajax/permissions',{params: params}).then(function(response) {
                    self.items = response.data.data.data;
                    self.totalPages = response.data.data.last_page;
                    cb();
                });
            }
        }
    }
</script>