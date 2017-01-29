module.exports = {
    'name': 'path',

    'el': '#path',

    data: function() {
        return _.merge({
            paths: false,
            config: {
                filter: this.$session.get('paths.filter', {order: 'id desc', limit:15})
            },
            pages: 0,
            count: '',
            selected: [],
        }, window.$data);
    },

    ready: function () {
        this.resource = this.$resource('api/cors/path{/id}');
        this.$watch('config.page', this.load, {immediate: true});    
    },

    watch: {

        'config.filter': {
            handler: function (filter) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }

                this.$session.set('paths.filter', filter);
            },
            deep: true
        }

    },

    methods: {
        active: function (path) {
            return this.selected.indexOf(path.id) != -1;
        },

        save: function (path) {
            this.resource.save({ id: path.id }, { path: path }).then(function () {
                this.load();
                this.$notify('Path saved.');
            });
        },

        status: function(status) {
            var paths = this.getSelected();

            for(var i = 0; i < paths.length; i++) {
                paths[i].status = status;
            }

            this.resource.save({ id: 'bulk' }, { paths: paths }).then(function () {
                this.load();
                this.$notify('Paths saved.');
            });
        },

        remove: function() {

            this.resource.delete({ id: 'bulk' }, { ids: this.selected }).then(function () {
                this.load();
                this.$notify('Paths deleted.');
            });
        },

        toggleStatus: function (path, field) {
            path[field] = !path[field];
            this.save(path);
        },

        load: function () {
            this.resource.query({ filter: this.config.filter, page: this.config.page }).then(function (res) {

                var data = res.data;

                this.$set('paths', data.paths);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('selected', []);
            });
        },

        getSelected: function() {
            return this.paths.filter(function(path) { return this.selected.indexOf(path.id) !== -1; }, this);
        }
    }
}

Vue.ready(module.exports);