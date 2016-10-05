import moment from 'moment';

Vue.component('activity', {

    props: ['user', 'team', 'filter'],

    mixins: [require('./../../spark/mixins/tab-state')],

    data() {
        return {
            loading: true,
            pagination: null,
            prospects: [],
            prospectIgnored: false,
            prospectTracked: false,
        }
    },

    computed: {
        endpoint() {
            let endpoint = '/api/activity';

            if(this.filter) {
                return `${endpoint}/${this.filter}`;
            }

            return endpoint;
        }
    },

    ready() {
        this.$http.get(this.endpoint).then((response) => {
            this.prospects = response.data.data;
            delete response.data.data;
            this.pagination = response.data;
            this.loading = false;
        });
    },

    methods: {
        activity(date) {
            return moment(date).fromNow(true);
        },

        loadMore($event) {
            $($event.target).button('loading');
            this.$http.get(this.pagination.next_page_url).then((response) => {
                this.prospects = this.prospects.concat(response.data.data);
                delete response.data.data;
                this.pagination = response.data;
                $($event.target).button('reset');
            });
        },

        track(prospect, undo = false) {
            this.$http.put(`/api/prospects/${prospect.id}`, {is_ignored: false}).then(() => {
                if(undo) {
                    this.prospects.push(prospect);
                    this.prospectIgnored = false;
                    return;
                }
                this.prospects.$remove(prospect);

                this.prospectTracked = prospect;

                setTimeout(() => {
                    this.prospectTracked = false;
                }, 5000);
            });
        },

        ignore(prospect, undo = false) {
            this.$http.put(`/api/prospects/${prospect.id}`, {is_ignored: true}).then(() => {
                if(undo) {
                    this.prospects.push(prospect);
                    this.prospectTracked = false;
                    return;
                }
                this.prospects.$remove(prospect);

                this.prospectIgnored = prospect;

                setTimeout(() => {
                    this.prospectIgnored = false;
                }, 5000);
            });
        },

        assign(prospect, user) {
            prospect.assignee = user;
            this.$http.put(`/api/prospects/${prospect.id}`, {assignee_id: user.id});
        },

        unassign(prospect) {
            prospect.assignee = null;
            this.$http.put(`/api/prospects/${prospect.id}`, { assignee_id: 0 });
        }
    }
});