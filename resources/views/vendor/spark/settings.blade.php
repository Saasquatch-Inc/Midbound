@extends('spark::layouts.app')

@section('scripts')
    @if (Spark::billsUsingStripe())
        <script src="https://js.stripe.com/v2/"></script>
    @else
        <script src="https://js.braintreegateway.com/v2/braintree.js"></script>
    @endif
@endsection

@section('content')
<spark-settings :user="user" :teams="teams" inline-template>
    <div class="spark-screen container">
        <div class="row">
            <!-- Tabs -->
            <div class="col-sm-3 col-md-2 spark-settings-tabs">
                <ul class="list-group" role="tablist">
                    <li class="list-header">My Account</li>
                    <!-- Profile Link -->
                    <li role="presentation" class="list-group-item active">
                        <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                            <i class="fa fa-fw fa-btn fa-user"></i>Profile
                        </a>
                    </li>

                    <!-- Security Link -->
                    <li role="presentation" class="list-group-item">
                        <a href="#password" aria-controls="security" role="tab" data-toggle="tab">
                            <i class="fa fa-fw fa-btn fa-lock"></i>Password
                        </a>
                    </li>

                    <!-- Teams Link -->
                    @if (Spark::usesTeams())
                        <li role="presentation" class="list-group-item">
                            <a href="#teams" aria-controls="teams" role="tab" data-toggle="tab">
                                <i class="fa fa-fw fa-btn fa-users"></i>Teams
                            </a>
                        </li>
                    @endif

                    <!-- API Link -->
                    {{--@if (Spark::usesApi())--}}
                        {{--<li role="presentation" class="list-group-item">--}}
                            {{--<a href="#api" aria-controls="api" role="tab" data-toggle="tab">--}}
                                {{--<i class="fa fa-fw fa-btn fa-key"></i>API Tokens--}}
                            {{--</a>--}}
                        {{--</li>--}}
                    {{--@endif--}}

                    
                </ul>
            </div>
            <div class="col-md-4">
                <!-- Billing Tabs -->
                @if (Spark::canBillCustomers())
                    <div class="panel panel-default panel-flush">
                        <div class="panel-heading">
                            Billing
                        </div>

                        <div class="panel-body">
                            <div class="spark-settings-tabs">
                                <ul class="nav spark-settings-stacked-tabs" role="tablist">
                                    @if (Spark::hasPaidPlans())
                                        <!-- Subscription Link -->
                                        <li role="presentation">
                                            <a href="#subscription" aria-controls="subscription" role="tab" data-toggle="tab">
                                                <i class="fa fa-fw fa-btn fa-shopping-bag"></i>Subscription
                                            </a>
                                        </li>
                                    @endif

                                    <!-- Payment Method Link -->
                                    <li role="presentation">
                                        <a href="#payment-method" aria-controls="payment-method" role="tab" data-toggle="tab">
                                            <i class="fa fa-fw fa-btn fa-credit-card"></i>Payment Method
                                        </a>
                                    </li>

                                    <!-- Invoices Link -->
                                    <li role="presentation">
                                        <a href="#invoices" aria-controls="invoices" role="tab" data-toggle="tab">
                                            <i class="fa fa-fw fa-btn fa-history"></i>Invoices
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tab Panels -->
            <div class="col-sm-9 col-md-10">
                <div class="tab-content">
                    <!-- Profile -->
                    <div role="tabpanel" class="tab-pane active" id="profile">
                        @include('spark::settings.profile')
                    </div>

                    <!-- Teams -->
                    @if (Spark::usesTeams())
                        <div role="tabpanel" class="tab-pane" id="teams">
                            @include('spark::settings.teams')
                        </div>
                    @endif

                    <!-- Security -->
                    <div role="tabpanel" class="tab-pane" id="password">
                        @include('spark::settings.password')
                    </div>

                    <!-- API -->
                    {{--@if (Spark::usesApi())--}}
                        {{--<div role="tabpanel" class="tab-pane" id="api">--}}
                            {{--@include('spark::settings.api')--}}
                        {{--</div>--}}
                    {{--@endif--}}

                    <!-- Billing Tab Panes -->
                    @if (Spark::canBillCustomers())
                        @if (Spark::hasPaidPlans())
                            <!-- Subscription -->
                            <div role="tabpanel" class="tab-pane" id="subscription">
                                <div v-if="user">
                                    @include('spark::settings.subscription')
                                </div>
                            </div>
                        @endif

                        <!-- Payment Method -->
                        <div role="tabpanel" class="tab-pane" id="payment-method">
                            <div v-if="user">
                                @include('spark::settings.payment-method')
                            </div>
                        </div>

                        <!-- Invoices -->
                        <div role="tabpanel" class="tab-pane" id="invoices">
                            @include('spark::settings.invoices')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</spark-settings>
@endsection
