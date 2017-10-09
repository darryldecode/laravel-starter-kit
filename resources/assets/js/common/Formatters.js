export default {

    install(Vue, options) {

        Vue.prototype.$appFormatters = {
            formatDate: function(dateString,format) {
                return moment(dateString).format(format ? format : 'MMMM DD, YYYY');
            }
        }
    }
}