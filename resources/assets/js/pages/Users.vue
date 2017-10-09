<template>
    <div class="users_view">
        <v-tabs dark v-model="active">
            <v-tabs-bar class="cyan">
                <v-tabs-item href="#users" ripple>
                    Users
                </v-tabs-item>
                <v-tabs-item href="#groups" ripple>
                    Groups
                </v-tabs-item>
                <v-tabs-item href="#permissions" ripple>
                    Permissions
                </v-tabs-item>
                <v-tabs-slider class="yellow"></v-tabs-slider>
            </v-tabs-bar>
            <v-tabs-items>
                <v-tabs-content id="users">
                    <v-card flat>
                        <v-card-text>
                            <user-lists></user-lists>
                        </v-card-text>
                    </v-card>
                </v-tabs-content>
                <v-tabs-content id="groups">
                    <v-card flat>
                        <v-card-text>
                            <group-lists></group-lists>
                        </v-card-text>
                    </v-card>
                </v-tabs-content>
                <v-tabs-content id="permissions">
                    <v-card flat>
                        <v-card-text>
                            <permission-lists></permission-lists>
                        </v-card-text>
                    </v-card>
                </v-tabs-content>
            </v-tabs-items>
        </v-tabs>
    </div>
</template>

<script>
    import UserLists from '../components/UserLists.vue';
    import GroupLists from '../components/GroupLists.vue';
    import PermissionLists from '../components/PermissionLists.vue';
    export default {
        components: {
            UserLists,
            GroupLists,
            PermissionLists
        },
        data() {
            return {
                active: ''
            }
        },
        mounted() {
            console.log('pages.Users.vue');

            const self = this;

            setTimeout(()=>{
                self.$store.commit('hideLoader');
            },1500);
        }
    }
</script>