module.exports = {

    'el' : '#cors',

    'data': {
        'cors': window.$data.cors,
        'form': {},
        'allow_origin_new' : '',
        'allow_headers_new' : '',
        'expose_headers_new': '',
        'hosts_new': '',
        'allow_methods_new': ''
    },

    'methods' : {

        'add' : function(field) {
            if(!this[field + "_new"]) return;
            this.cors[field].push(this[field + "_new"]);
            this[field + "_new"] = '';
        },

        'remove' : function(entry, field) {
            this.cors[field].$remove(entry);
        },

        'save': function() {
            this.$http.post('admin/cors/save', { cors: this.cors }).then(function () {
                    this.$notify('Settings saved.');
                }, function (res) {
                    this.$notify(res.data, 'danger');
                }
            );
        } 
        
    }
}

Vue.ready(module.exports);