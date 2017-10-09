<template>
    <div>
        SETTINGS VUE
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('pages.Settings.vue');

            const self = this;

            setTimeout(()=>{
                self.$store.commit('hideLoader');
            },1500);
        }
    }
</script>