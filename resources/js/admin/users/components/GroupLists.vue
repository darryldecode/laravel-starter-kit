<template>
    <div class="component-wrap">

        <!-- search -->
        <v-card>
            <div class="d-flex flex-row">
                <div class="flex-grow-1 pa-2">
                    <v-text-field prepend-icon="search" label="Filter By Name" v-model="filters.name"></v-text-field>
                </div>
                <div class="flex-grow-1 pa-2 text-right">
                    <v-btn @click="$router.push({name:'users.groups.create'})" class="primary lighten-1" dark>
                        New Group
                        <v-icon right>add</v-icon>
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
                        <v-btn @click="$router.push({name:'users.groups.edit',params:{id:item.id}})" class="ma-2" outlined fab small color="info">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn @click="trash(item)" class="ma-2" outlined fab small color="red">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </td>
                    <td>{{ item.name }}</td>
                    <td>
                        <v-btn small @click="showDialog('group_permissions',item.permissions)" outlined rounded color="grey" dark>Show</v-btn>
                    </td>
                    <td>{{ item.members_count }}</td>
                    <td>{{ $appFormatters.formatDate(item.created_at) }}</td>
                </tr>
                </tbody>
            </template>
        </v-data-table>

        <!-- dialog for show permissions -->
        <v-dialog v-model="dialogs.showPermissions.show" absolute max-width="300px">
            <v-card>
                <v-card-title>
                    <div class="headline"><v-icon>vpn_key</v-icon> Group Permissions</div>
                </v-card-title>
                <v-card-text>
                    <v-chip v-for="(permission,key) in dialogs.showPermissions.items" :key="key" class="white--text" :class="{'green':(permission.value==1),'red':(permission.value==-1),'blue':(permission.value==0)}">
                        <v-avatar v-if="permission.value==-1" class="red darken-4" title="Deny">
                            <v-icon>block</v-icon>
                        </v-avatar>
                        <v-avatar v-if="permission.value==1" class="green darken-4" title="Allow">
                            <v-icon>check_circle</v-icon>
                        </v-avatar>
                        <v-avatar v-if="permission.value==0" class="blue darken-4" title="Inherit">
                            <v-icon>swap_horiz</v-icon>
                        </v-avatar>
                        {{permission.title}}
                    </v-chip>
                    <p v-if="dialogs.showPermissions.items.length==0">No permissions</p>
                </v-card-text>
            </v-card>
        </v-dialog>

    </div>
</template>

<script>
    export default {
        data () {
            return {
                headers: [
                    { text: 'Action', value: false, align: 'left', sortable: false },
                    { text: 'Name', value: 'name', align: 'left', sortable: false },
                    { text: 'Permissions', value: 'permissions', align: 'left', sortable: false },
                    { text: 'Total Members', value: 'members_count', align: 'left', sortable: false },
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
                    showPermissions: {
                        items: [],
                        show: false
                    },
                }
            }
        },
        mounted() {
            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'Users',to:{name:'users.list'}},
                {label:'Groups',to:''},
            ]);
        },
        watch: {
            'pagination.page':function(){
                this.loadGroups();
            },
            'pagination.rowsPerPage':function(){
                this.loadGroups();
            },
            'filters.name':_.debounce(function(){
                const self = this;
                self.loadGroups();
            },700),
        },
        methods: {
            trash(group) {
                const self = this;

                self.$store.commit('showDialog',{
                    type: "confirm",
                    title: "Confirm Deletion",
                    message: "Are you sure you want to delete this group?",
                    okCb: ()=>{

                        axios.delete('/admin/groups/' + group.id).then(function(response) {

                            self.$store.commit('showSnackbar',{
                                message: response.data.message,
                                color: 'success',
                                duration: 3000
                            });

                            self.$eventBus.$emit('GROUP_DELETED');

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
            showDialog(dialog, data) {

                const self = this;

                switch (dialog){
                    case 'group_permissions':
                        self.dialogs.showPermissions.items = data;
                        setTimeout(()=>{
                            self.dialogs.showPermissions.show = true;
                        },500);
                        break;
                }
            },
            loadGroups(cb) {

                const self = this;

                let params = {
                    name: self.filters.name,
                    page: self.pagination.page,
                    per_page: self.pagination.rowsPerPage
                };

                axios.get('/admin/groups',{params: params}).then(function(response) {
                    self.items = response.data.data.data;
                    self.totalItems = response.data.data.total;
                    self.pagination.totalItems = response.data.data.total;
                    (cb || Function)();
                });
            }
        }
    }
</script>