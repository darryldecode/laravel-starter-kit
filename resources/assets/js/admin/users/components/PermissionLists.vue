<template>
    <div class="component-wrap">

        <!-- search -->
        <v-card dark>
            <v-layout row wrap>
                <v-flex xs12 class="px-2 pt-2">
                    <v-btn @click="$router.push({name:'users.permissions.create'})" class="blue lighten-1" dark>
                        New Permission
                        <v-icon right dark>add</v-icon>
                    </v-btn>
                </v-flex>
                <v-flex xs12 class="px-2">
                    <v-text-field prepend-icon="search" box dark label="Filter By Permission Title" v-model="filters.title"></v-text-field>
                </v-flex>
            </v-layout>
        </v-card>
        <!-- /search -->

        <!-- groups table -->
        <v-data-table
                v-bind:headers="headers"
                v-bind:pagination.sync="pagination"
                :items="items"
                :total-items="totalItems"
                class="elevation-1">
            <template slot="headerCell" slot-scope="props">
                <span v-if="props.header.value=='key'">
                    <v-icon>vpn_key</v-icon> {{ props.header.text }}
                </span>
                <span v-else-if="props.header.value=='created_at'">
                    <v-icon>date_range</v-icon> {{ props.header.text }}
                </span>
                <span v-else>{{ props.header.text }}</span>
            </template>
            <template slot="items" slot-scope="props">
                <td>
                    <v-menu>
                        <v-btn icon slot="activator" dark>
                            <v-icon>more_vert</v-icon>
                        </v-btn>
                        <v-list>
                            <v-list-tile @click="$router.push({name:'users.permissions.edit',params:{id:props.item.id}})">
                                <v-list-tile-title>Edit</v-list-tile-title>
                            </v-list-tile>
                            <v-list-tile @click="trash(props.item)">
                                <v-list-tile-title>Delete</v-list-tile-title>
                            </v-list-tile>
                        </v-list>
                    </v-menu>
                </td>
                <td>{{ props.item.title }}</td>
                <td>{{ props.item.key }}</td>
                <td>{{ props.item.description }}</td>
                <td>{{ $appFormatters.formatDate(props.item.created_at) }}</td>
            </template>
        </v-data-table>

    </div>
</template>

<script>
    export default {
        data () {
            return {
                headers: [
                    { text: 'Action', value: false, align: 'left', sortable: false },
                    { text: 'Title', value: 'name', align: 'left', sortable: false },
                    { text: 'Key', value: 'key', align: 'left', sortable: false },
                    { text: 'Description', value: 'description', align: 'left', sortable: false },
                    { text: 'Date Created', value: 'created_at', align: 'left', sortable: false },
                ],
                items: [],
                totalItems: 0,
                pagination: {
                    rowsPerPage: 10
                },

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

            self.$store.commit('setBreadcrumbs',[
                {label:'Users',name:'users.list'},
                {label:'Permissions',name:''},
            ]);
        },
        watch: {
            'pagination.page':function(){
                this.loadPermissions(()=>{});
            },
            'pagination.rowsPerPage':function(){
                this.loadPermissions(()=>{});
            },
            'filters.title':_.debounce(function(){
                const self = this;
                self.loadPermissions(()=>{});
            },700),
        },
        methods: {
            trash(permission) {
                const self = this;

                self.$store.commit('showDialog',{
                    type: "confirm",
                    title: "Confirm Deletion",
                    message: "Are you sure you want to delete this permission?",
                    okCb: ()=>{

                        axios.delete('/admin/permissions/' + permission.id).then(function(response) {

                            self.$store.commit('showSnackbar',{
                                message: response.data.message,
                                color: 'success',
                                duration: 3000
                            });

                            self.loadPermissions(()=>{});

                        }).catch(function (error) {

                            self.$store.commit('hideLoader');

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
            loadPermissions(cb) {

                const self = this;

                let params = {
                    title: self.filters.title,
                    page: self.pagination.page,
                    per_page: self.pagination.rowsPerPage
                };

                axios.get('/admin/permissions',{params: params}).then(function(response) {
                    self.items = response.data.data.data;
                    self.totalItems = response.data.data.total;
                    self.pagination.totalItems = response.data.data.total;
                    (cb || Function)();
                });
            }
        }
    }
</script>