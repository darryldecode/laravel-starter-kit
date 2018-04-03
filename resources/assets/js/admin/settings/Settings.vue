<template>
    <div class="page_wrap_vue pa-3">
        <h2>SETTINGS PAGE</h2>
        <p>I am located at <b>resources/assets/js/admin/pages/settings/Settings.vue</b></p>
        <p>single page routes can be found at <b>resources/assets/js/router</b></p>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('pages.Settings.vue');

            const self = this;

            self.$store.commit('setBreadcrumbs',[
                {label:'Settings',name:''}
            ]);
        }
    }
</script>