@extends('layouts.admin')

@section('content')
    <template>
        <v-app id="inspire" dark>
            <v-navigation-drawer
                    clipped
                    fixed
                    v-model="drawer"
                    app>
                <v-list dense>

                    @foreach($nav as $n)
                        @if($n['nav_type']=='nav')
                            <v-list-tile @click="menuClick('{{$n['route_name']}}','{{$n['route_type']}}')">
                                <v-list-tile-action>
                                    <v-icon>{{$n['icon']}}</v-icon>
                                </v-list-tile-action>
                                <v-list-tile-content>
                                    <v-list-tile-title>{{$n['label']}}</v-list-tile-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        @else
                            <v-divider></v-divider>
                        @endif
                    @endforeach

                    <v-list-tile @click="clickLogout('{{route('logout')}}','{{route('login')}}')">
                        <v-list-tile-action>
                            <v-icon>directions_walk</v-icon>
                        </v-list-tile-action>
                        <v-list-tile-content>
                            <v-list-tile-title>Logout</v-list-tile-title>
                        </v-list-tile-content>
                    </v-list-tile>

                </v-list>
            </v-navigation-drawer>
            <v-toolbar app fixed clipped-left>
                <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
                <v-toolbar-title>{{config('app.name')}}</v-toolbar-title>
            </v-toolbar>
            <v-content>
                <router-view></router-view>
            </v-content>
            <v-footer app fixed>
                <span>&copy; 2017</span>
            </v-footer>
        </v-app>

        <!-- loader -->
        <div v-if="showLoader" class="wask_loader bg_half_transparent">
            <moon-loader color="red"></moon-loader>
        </div>

        <!-- snackbar -->
        <v-snackbar
                :timeout="snackbarDuration"
                :color="snackbarColor"
                top
                v-model="showSnackbar">
            @{{ snackbarMessage }}
        </v-snackbar>

        <!-- dialog confirm -->
        <v-dialog v-show="showDialog" v-model="showDialog" lazy absolute max-width="450px">
            <v-btn color="primary" dark slot="activator">Open Dialog</v-btn>
            <v-card>
                <v-card-title>
                    <div class="headline">@{{ dialogTitle }}</div>
                </v-card-title>
                <v-card-text>@{{ dialogMessage }}</v-card-text>
                <v-card-actions v-if="dialogType=='confirm'">
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" flat="flat" @click.native="dialogCancel">Cancel</v-btn>
                    <v-btn color="green darken-1" flat="flat" @click.native="dialogOk">Ok</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </template>
@endsection
