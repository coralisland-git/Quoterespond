<div class="page page-table" data-ng-controller="PlansCtrl" data-ng-init="initPlanPage()">
    <div class="row">
        <div class="col-sm-12 col-md-6 plans">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="loader_wrapper">
                        <div class="content-loader" ng-show="! request_finish">
                            <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                        </div>
                    </div>

                    <div class="row">
						<div class="col-sm-12">
                            <form id="payment-form" ng-show="showCardDetails && request_finish">
                                <h5>
                                    Please enter your CC. No contracts, no commitment, no setup fee, no cancellation fee.
                                    Billing is a simple $0.99 per Yelp Quote Request. Book a call with us <a href="https://calendly.com/quoterespond" target="_blank">HERE</a>
                                </h5>

                                <div class="form-row">
                                    <div class="card-element">
                                    </div>

                                    <div id="card-errors" role="alert"></div>
                                </div>

                                <button>Submit</button>
                                <a href="javascript:void(0);" class="btn btn-default" ng-show="stripe.stripe_id" ng-click="showCardDetails = ! showCardDetails">Cancel</a>
                            </form>

                            <div class="plan_details">
                                <div class="card_details" ng-show="stripe.stripe_id && ! showCardDetails && request_finish">
                                    <h5>Credit or Debit Card</h5>
                                    <div class="form-group pull-left">
                                        <i class="fa fa-cc-visa" ng-if="stripe.card_brand == 'Visa'"></i>
                                        <i class="fa fa-cc-mastercard" ng-if="stripe.card_brand == 'MasterCard'"></i>
                                        <i class="fa fa-cc-amex" ng-if="stripe.card_brand == 'American Express'"></i>
                                        <i class="fa fa-cc-discover" ng-if="stripe.card_brand == 'Discover'"></i>
                                        <i class="fa fa-cc-jcb" ng-if="stripe.card_brand == 'JCB'"></i>
                                        <i class="fa fa-cc-diners-club" ng-if="stripe.card_brand == 'Diners Club'"></i>
                                        <i class="fa fa-credit-card" ng-if="stripe.card_brand == 'UnionPay'"></i>
                                        <span>****@{{ stripe.card_last_four}}</span>
                                    </div>

                                    <button class="btn btn-primary pull-right" ng-click="showCardDetails = ! showCardDetails">Change</button>
                                </div>

                                <div class="cancel_subscription" ng-show="request_finish && stripe.stripe_id">
                                    <h5>
                                        Your Plan
                                    </h5>

                                    <table class="table table-bordered table-striped table-middle table-phones">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="th">
                                                        {{ __('Current Plan') }}
                                                    </div>
                                                </th>

                                                <th>
                                                    <div class="th">
                                                        {{ __('Subscription Status') }}
                                                    </div>
                                                </th>

                                                <th>
                                                    <div class="th">
                                                        {{ __('Cancel Subscription') }}
                                                    </div>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td>
                                                    @{{ plan_name }}
                                                </td>

                                                <td>
                                                    @{{ stripe.status ? stripe.status : 'Not Active' }}
                                                </td>

                                                <td class="text-center">
                                                    <button class="btn btn-default" ng-class="{disabled: ! stripe.stripe_id}" ng-click="cancelSubscription()">Cancel</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/ng-template" id="ModalCancelPlansConfirm.html">
	<form name="form" method="post" novalidate="novalidate">
		<div class="modal-header">
			<h4 class="modal-title">{{ __("Cancel subscription") }}</h4>
		</div>

		<div class="modal-body cancell-wrapper">
			<div class="row">
                <div class="content-loader" ng-show="! request_finish">
                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                </div>

				<div ng-show="showCancelReason && request_finish">
                    <div class="col-xs-12">
                        <div class="form-group text-center">
                            <p>Sorry to hear - if we can please ask, what’s the issue? Thanks!</p>
                            <p>
                                <button class="btn btn-primary" ng-click="clickReasonLeads()">{{ __('Not buying leads anymore') }}</button>
                                <button class="btn btn-primary" ng-click="clickReasonTexts()">{{ __('Texts are annoying customers') }}</button>
                                <button class="btn btn-primary" ng-click="clickReasonOther()">{{ __('Other') }}</button>
                            </p>
                        </div>
                    </div>
                </div>

                <div ng-show="reasonLeads && ! showCancelReason">
                    <div class="col-sm-12 text-center">
                        <div ng-show="reasonLeadsQuestion">
                            <p>Are you pausing leads or finished forever?</p>

                            <div class="form-group">
                                <button class="btn btn-primary" ng-click="clickReasonLeadsPausing()">{{ __('Pausing') }}</button>
                                <button class="btn btn-primary" ng-click="clickReasonLeadsFinished()">{{ __('Finished forever') }}</button>
                            </div>
                        </div>

                        <div ng-show="reasonPausing">
                            <p>
                                Great - we will pause your account with us, to save you time when you start buying leads again.
                                We will email you to follow-up, thanks.
                            </p>
                        </div>

                        <div ng-show="reasonFinished && request_finish">
                            <p>
                                No problem - canceling your account, thanks for your business!
                            </p>
                        </div>
                    </div>
                </div>

                <div ng-show="reasonTexts && ! showCancelReason">
                    <div class="col-sm-12 text-center">
                        <div ng-show="reasonTextsQuestion">
                            <p>
                                Sorry to hear - have you tried turning off the follow-ups, so you send less than 3 texts in total?
                                We can discount you to just $29/mo if you decide to stay and only send 1 text?
                            </p>

                            <div class="form-group">
                                <button class="btn btn-primary" ng-click="clickReasonTextsYes()">{{ __("Yes, we've tried less texts and it's still annoying customers") }}</button>
                            </div>

                            <div class="form-group">
                                <button class="btn btn-primary" ng-click="clickReasonTextsNo()">{{ __("No, we haven't tried less texts, I will stay with the discount") }}</button>
                            </div>
                        </div>

                        <div ng-show="reasonTextsCancell && request_finish">
                            <p>
                                No problem - canceling your account, thanks for your business!
                            </p>
                        </div>

                        <div ng-show="reasonTextsNotCancell">
                            <p>
                                Great - we will discount you and turn off your follow-ups - thanks!
                            </p>
                        </div>
                    </div>
                </div>

                <div ng-show="! showCancelReason && reasonOther && request_finish">
                    <div class="col-sm-12 text-center">
                        <h4>Why do you want to unsubscribe?</h4>
                        <div class="form-group">
                            <textarea name="reason" class="form-control" ng-model="plan.reason" maxlength="191"></textarea>
                        </div>
                        <button class="btn btn-primary" ng-class="{disabled: ! plan.reason}" ng-click="unsubscribe()">{{ __('Submit and Unsubscribe') }}</button>
                    </div>
                </div>
			</div>
		</div>

		<div class="modal-footer">
			<button type="button" class="btn btn-default" ng-click="cancel()">{{ __('Close') }}</button>
		</div>
	</form>
</script>