module.exports = {
    props: ['user', 'team'],

    /**
     * The component's data.
     */
    data() {
        return {
            form: new SparkForm({})
        };
    },


    methods: {
        /**
         * Update the team's photo.
         */
        update(e) {
            e.preventDefault();

            var self = this;

            this.form.startProcessing();

            // We need to gather a fresh FormData instance with the profile photo appended to
            // the data so we can POST it up to the server. This will allow us to do async
            // uploads of the profile photos. We will update the user after this action.
            this.$http.post(this.urlForUpdate, this.gatherFormData())
                .then(
                    () => {
                        Bus.$emit('updateTeam');
                        Bus.$emit('updateTeams');
                        self.form.finishProcessing();
                    },
                    (error) => {
                        self.form.setErrors(error.data.responseJSON);
                    }
                );
        },


        /**
         * Gather the form data for the photo upload.
         */
        gatherFormData() {
            const data = new FormData();

            data.append('photo', this.$refs.photo.files[0]);

            return data;
        }
    },


    computed: {
        /**
         * Get the URL for updating the team photo.
         */
        urlForUpdate() {
            return `/settings/${Spark.pluralTeamString}/${this.team.id}/photo`;
        },


        /**
         * Calculate the style attribute for the photo preview.
         */
        previewStyle() {
            return `background-image: url(${this.team.photo_url})`;
        }
    }
};
