module.exports = {

    'el' : '#path-edit',

    'data': {
        'data': window.$data,
        'path': window.$data.path,
        'form': {},
        'allow_origin_new' : '',
        'allow_headers_new' : '',
        'expose_headers_new': '',
        'hosts_new': '',
        'allow_methods_new': ''
    },

    ready: function () {
        this.resource = this.$resource('api/cors/path{/id}');
    },

    'methods' : {

        'add' : function(field) {
            if(!this[field + "_new"]) return;
            this.path[field].push(this[field + "_new"]);
            this[field + "_new"] = '';
        },

        'remove' : function(entry, field) {
            this.path[field].$remove(entry);
        },

        'save': function() {
            var data = {path: this.path, id: this.path.id};

            this.resource.save({id: this.path.id}, data).then(function (res) {

                var data = res.data;

                if (!this.path.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/blog/post/edit', {id: data.path.id}))
                }

                this.$set('path', data.path);

                this.$notify('Path saved.');

            }, function (res) {
                this.$notify(res.data, 'danger');
            });
        } 

    }
}

Vue.ready(module.exports);