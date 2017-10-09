<template>
    <div class="page_wrap_vue">
        <div class="finder_wrap">
            <div id="elfinder"></div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('pages.FileManager.vue');

            const self = this;

            setTimeout(()=>{
                self.$store.commit('hideLoader');
            },1500);
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