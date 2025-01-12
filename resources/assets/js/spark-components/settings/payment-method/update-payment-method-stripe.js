import Payment from 'payment';

var base = require('settings/payment-method/update-payment-method-stripe');

Vue.component('spark-update-payment-method-stripe', {
    props: ['user', 'team', 'billableType'],

    /**
     * The component's data.
     */
    data() {
        return {
            expiry: '',
            updating: false,
            form: new SparkForm({
                stripe_token: '',
                address: '',
                address_line_2: '',
                city: '',
                state: '',
                zip: '',
                country: 'US'
            }),

            cardForm: new SparkForm({
                name: '',
                number: '',
                cvc: '',
                month: '',
                year: ''
            })
        };
    },


    /**
     * Prepare the component.
     */
    mounted() {
        Stripe.setPublishableKey(Spark.stripeKey);

        this.initializeBillingAddress();
        this.initializeForm();
    },

    watch: {
        expiry(value) {
            console.log(value);

            let expiry = Payment.fns.cardExpiryVal(value);

            if(expiry.month) {
                this.cardForm.month = expiry.month;
            } else {
                this.cardForm.month = '';
            }

            if(expiry.year) {
                this.cardForm.year = expiry.year;
            } else {
                this.cardForm.year = '';
            }
        }
    },

    methods: {
        /**
         * Initialize the billing address form for the billable entity.
         */
        initializeBillingAddress() {
            if (! Spark.collectsBillingAddress) {
                return;
            }

            this.form.address = this.billable.billing_address;
            this.form.address_line_2 = this.billable.billing_address_line_2;
            this.form.city = this.billable.billing_city;
            this.form.state = this.billable.billing_state;
            this.form.zip = this.billable.billing_zip;
            this.form.country = this.billable.billing_country || 'US';
        },

        initializeForm() {
            Payment.formatCardNumber($('.update-form input[data-stripe="number"]'));
            Payment.formatCardExpiry($('.update-form input[data-stripe="exp"]'));
            Payment.formatCardCVC($('.update-form input[data-stripe="cvc"]'));
        },

        toggleUpdate() {
            if(this.updating) {
                this.cardForm.name = '';
                this.cardForm.number = '';
                this.cardForm.cvc = '';
                this.cardForm.month = '';
                this.cardForm.year = '';
                this.expiry = '';
                this.updating = false;
            } else {
                this.updating = true;
                this.$nextTick(() => {
                    this.initializeForm();
                })
            }
        },

        /**
         * Update the billable's card information.
         */
        update() {
            this.form.busy = true;
            this.form.errors.forget();
            this.form.successful = false;
            this.cardForm.errors.forget();

            // Here we will build out the payload to send to Stripe to obtain a card token so
            // we can create the actual subscription. We will build out this data that has
            // this credit card number, CVC, etc. and exchange it for a secure token ID.
            const payload = {
                name: this.cardForm.name,
                number: this.cardForm.number,
                cvc: this.cardForm.cvc,
                exp_month: this.cardForm.month,
                exp_year: this.cardForm.year,
                address_line1: this.form.address,
                address_line2: this.form.address_line_2,
                address_city: this.form.city,
                address_state: this.form.state,
                address_zip: this.form.zip,
                address_country: this.form.country,
            };

            // Once we have the Stripe payload we'll send it off to Stripe and obtain a token
            // which we will send to the server to update this payment method. If there is
            // an error we will display that back out to the user for their information.
            Stripe.card.createToken(payload, (status, response) => {
                if (response.error) {
                    this.cardForm.errors.set({number: [
                        response.error.message
                    ]});

                    this.form.busy = false;
                } else {
                    this.sendUpdateToServer(response.id);
                }
            });
        },

        /**
         * Send the credit card update information to the server.
         */
        sendUpdateToServer(token) {
            this.form.stripe_token = token;

            Spark.put(this.urlForUpdate, this.form)
                .then(() => {
                    Bus.$emit('updateUser');
                    Bus.$emit('updateTeam');

                    this.cardForm.name = '';
                    this.cardForm.number = '';
                    this.cardForm.cvc = '';
                    this.cardForm.month = '';
                    this.cardForm.year = '';
                    this.expiry = '';

                    if ( ! Spark.collectsBillingAddress) {
                        this.form.zip = '';
                    }

                    this.updating = false;
                });
        }
    },


    computed: {
        /**
         * Get the billable entity's "billable" name.
         */
        billableName() {
            return this.billingUser ? this.user.name : this.team.owner.name;
        },


        /**
         * Get the URL for the payment method update.
         */
        urlForUpdate() {
            return this.billingUser
                ? '/settings/payment-method'
                : `/settings/${Spark.pluralTeamString}/${this.team.id}/payment-method`;
        },

        /**
         * Get the new card brand
         */
        cardType() {
            return Payment.fns.cardType(this.cardForm.number);
        },

        /**
         * Get the proper brand icon for the customer's credit card.
         */
        cardIcon() {
            if (! this.billable.card_brand) {
                return 'fa-cc-stripe';
            }

            switch (this.billable.card_brand) {
                case 'American Express':
                    return 'fa-cc-amex';
                case 'Diners Club':
                    return 'fa-cc-diners-club';
                case 'Discover':
                    return 'fa-cc-discover';
                case 'JCB':
                    return 'fa-cc-jcb';
                case 'MasterCard':
                    return 'fa-cc-mastercard';
                case 'Visa':
                    return 'fa-cc-visa';
                default:
                    return 'fa-cc-stripe';
            }
        },

        /**
         * Get the placeholder for the billable entity's credit card.
         */
        placeholder() {
            if (this.billable.card_last_four) {
                return `************${this.billable.card_last_four}`;
            }

            return '';
        }
    }
});
