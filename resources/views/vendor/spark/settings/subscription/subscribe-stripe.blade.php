<spark-subscribe-stripe :user="user" :team="team"
                :plans="plans" :billable-type="billableType" inline-template>

    <div>
        <!-- Common Subscribe Form Contents -->
        @include('spark::settings.subscription.subscribe-common')

        <!-- Billing Information -->
        <div class="panel panel-default" v-show="selectedPlan">

            <div class="panel-heading">Billing Information</div>

            <div class="panel-body">
                <!-- Generic 500 Level Error Message / Stripe Threw Exception -->
                <div class="alert alert-danger" v-if="form.errors.has('form')">
                    We had trouble validating your card. It's possible your card provider is preventing
                    us from charging the card. Please contact your card provider or customer support.
                </div>

                <form class="form-horizontal subscribe-form" role="form">
                    <!-- Billing Address Fields -->
                    @if (Spark::collectsBillingAddress())
                        <h2><i class="fa fa-btn fa-map-marker"></i>Billing Address</h2>

                        @include('spark::settings.subscription.subscribe-address')

                        <h2><i class="fa fa-btn fa-credit-card"></i>Credit Card</h2>
                @endif

                <!-- Cardholder's Name -->
                    <div class="form-group">
                        <label for="name" class="col-md-4 control-label">Cardholder's Name</label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" v-model="cardForm.name" autofocus>
                        </div>
                    </div>

                    <!-- Card Number -->
                    <div class="form-group" :class="{'has-error': cardForm.errors.has('number')}">

                        <label for="number" class="col-md-4 control-label">Card Information</label>

                        <div class="col-md-6">
                            <i v-if="cardType" :class="['fa', 'fa-input', cardIcon]"></i>
                            <input type="text"
                                   class="form-control"
                                   data-stripe="number"
                                   :placeholder="placeholder"
                                   v-model="cardForm.number">
                        </div>
                    </div>

                    <!-- Security Code & Expiration -->
                    <div class="form-group" :class="{'has-error': cardForm.errors.has('number')}">
                        <label for="cvc" class="col-md-4 control-label"></label>

                        <div class="col-md-3">
                            <input type="text" class="form-control"
                                   placeholder="MM / YY" data-stripe="exp" v-model="expiry">
                        </div>

                        <div class="col-md-3">
                            <input type="text" placeholder="CVC" class="form-control" data-stripe="cvc" v-model="cardForm.cvc">
                        </div>

                        <span class="col-md-6 col-md-offset-4 help-block" v-show="cardForm.errors.has('number')">
                            @{{ cardForm.errors.get('number') }}
                        </span>
                    </div>

                    <!-- ZIP Code -->
                    <div class="form-group" v-if=" ! spark.collectsBillingAddress">
                        <label for="number" class="col-md-4 control-label">ZIP / Postal Code</label>

                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="zip" v-model="form.zip">
                        </div>
                    </div>

                    <!-- Coupon -->
                    <div class="form-group" :class="{'has-error': form.errors.has('coupon')}">
                        <label class="col-md-4 control-label">Coupon</label>

                        <div class="col-sm-6">
                            <input type="text" class="form-control" v-model="form.coupon">

                            <span class="help-block" v-show="form.errors.has('coupon')">
                            @{{ form.errors.get('coupon') }}
                        </span>
                        </div>
                    </div>

                    <!-- Tax / Price Information -->
                    <div class="form-group" v-if="spark.collectsEuropeanVat && countryCollectsVat && selectedPlan">
                        <label class="col-md-4 control-label">&nbsp;</label>

                        <div class="col-md-6">
                            <div class="alert alert-info" style="margin: 0;">
                                <strong>Tax:</strong> @{{ taxAmount(selectedPlan) | currency spark.currencySymbol }}
                                <br><br>
                                <strong>Total Price Including Tax:</strong>
                                @{{ priceWithTax(selectedPlan) | currency spark.currencySymbol }} / @{{ selectedPlan.interval | capitalize }}
                            </div>
                        </div>
                    </div>

                    <!-- Subscribe Button -->
                    <div class="form-group">
                        <div class="col-sm-6 col-sm-offset-4">
                            <button type="submit" class="btn btn-primary" @click.prevent="subscribe" :disabled="form.busy">
                                <span v-if="form.busy">
                                    <i class="fa fa-btn fa-spinner fa-spin"></i>Subscribing
                                </span>

                                <span v-else>
                                    Subscribe
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</spark-subscribe-stripe>
