export default {

    install(Vue, options) {

        Vue.prototype.$appFormatters = {
            formatDate: function(dateString,format) {
                return moment(dateString).format(format ? format : 'MMMM DD, YYYY');
            },
            formatByteToMB (sizeInBytes) {
                return (sizeInBytes / (1024*1024)).toFixed(2);
            },
            formatMbToBytes (mb) {
                return (mb * 1048576).toFixed(2);
            },
        }
    }
}