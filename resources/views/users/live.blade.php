<div class="page page-table" data-ng-controller="UsersCtrl" data-ng-init="initLive()">
	<h2>
		<div class="pull-right">
			<button class="btn btn-primary btn-danger" ng-click="addMailbox()">Add mailbox</button>
		</div>
		
		<div class="search-bar pull-right">
			<input type="text" class="form-control" ng-model="quickSearch" placeholder="{{ __('Quick Search...') }}" />
		</div>

		{{ __('Live Users') }}
	</h2>

	<div class="content-loader" ng-show=" ! request_finish">
		<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
	</div>

	<div ng-show="request_finish">
		<div uib-alert class="alert-warning" ng-show="(list | filter : quickSearch).length == 0">
			{{ __("Nothing found.") }}
		</div>

		<section class="panel panel-default table-dynamic table-responsive " ng-show="(list | filter : quickSearch).length">
			<table class="table table-bordered table-striped table-middle">
				<thead>
					<tr>
						<th>
							<div class="th">
								{{ __('First Name') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Last Name') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Email') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Cell #') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Current Plan') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('CC') }}
							</div>
						</th>

						<th class="th-button">
							<div class="tiny-th">
								{{ __('User Settings') }}
							</div>
						</th>

						<th class="th-button">
							<div class="tiny-th">
								{{ __('Allow access') }}
							</div>
						</th>

						<th class="th-button">
						</th>

						<th class="th-button">
						</th>

						<th class="th-button">
						</th>
					</tr>
				</thead>

				<tbody>
					<tr ng-repeat="user in list | filter : quickSearch">
						<td>
							@{{ user.firstname }}
						</td>

						<td>
							@{{ user.lastname }}
						</td>

						<td>
							<div class="admin_email_wrapper">
								@{{ user.email }}
							</div>
						</td>

						<td>
							@{{ user.view_phone }}
						</td>

						<td>
							@{{ user.current_plan }}
						</td>

						<td class="td-button text-center">
							<span class="check-span" ng-if="user.has_subscription"><i class="fa fa-check"></i></span>
							<span class="times-span" ng-if=" ! user.has_subscription"><i class="fa fa-times"></i></span>
						</td>

						<td class="td-button text-center">
							<button class="btn btn-primary btn-danger" ng-click="settings(user)">Settings</button>
						</td>

						<td class="td-button text-center">
							<div class="access_switcher">
								<label class="ui-switch ui-switch-success ui-switch-sm">
									<input id="allow_access" type="checkbox" ng-click="allowAccess(user.id)" ng-model="user.allow_access" ng-true-value="1" ng-false-value="0" />
									<i></i>
								</label>
							</div>
						</td>

						<td class="td-button text-center">
							<a href="javascript:;" class="a-icon text-warning" ng-click="magic(user.id)">
								<i class="fa fa-lock" aria-hidden="true"></i>
							</a>
						</td>

						<td class="td-button text-center">
							<a href="javascript:;" class="a-icon text-success" ng-click="create(user.id)">
								<i class="fa fa-pencil-square-o"></i>
							</a>
						</td>

						<td class="td-button text-center">
							<a href="javascript:;" class="a-icon text-danger" ng-click="remove(user.id)">
								<i class="fa fa-trash"></i>
							</a>
						</td>
					</tr>
				</tbody>
			</table>
		</section>
	</div>
</div>

<script type="text/ng-template" id="ModalSettings.html">
	<form name="form" method="post" novalidate="novalidate">
		<div class="modal-header">
			<h4 class="modal-title">{{ __("User Settings") }}</h4>
		</div>

		<div class="modal-body">
			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						<label>Cancel Subscription</label><br />
						<button class="btn btn-primary btn-danger" ng-click="confirmSubscription(user, 'cancel')">Cancel</button>
					</div>

					<div class="form-group">
						<label>Assign Plan</label><br />
						<button class="btn btn-primary btn-danger" ng-click="confirmSubscription(user, 'assign')">Assign</button>
					</div>

					<div class="form-group">
						<label>Assign Company</label><br />
						<button class="btn btn-primary btn-danger" ng-click="confirmSubscription(user, 'assign_company')">Assign</button>
					</div>
				</div>

				<div class="col-sm-9">
					<div class="form-group">
						<div class="input-group mb-3">
							<label for="forwarding_email">Forwarding Email</label>
							<input type="text" class="form-control" id="forwarding_email" ng-model="user.forwarding_email" placeholder="Forwarding Email" />
							<div class="input-group-append">
								<button class="btn btn-primary btn-danger" ng-click="addForwardingEmail(user)">Save</button>
							</div>
						</div>
					</div>

					<div class="row" ng-show="user.plans_id == 'star-rating-contractortexter'">
						<div class="col-sm-9">
							<div class="form-group">
								<label for="google_link">
									<img src="https://www.google.com/s2/favicons?domain=http://google.com" alt="" />
									<strong>Google URL</strong>
								</label>
								<input type="text" class="form-control" id="google_link" ng-model="googleUrl.url" placeholder="Google Link" ng-blur="saveGoogleUrl()" />
							</div>
						</div>

						<div class="col-sm-3">
							<label id="google-link-toggle" class="ui-switch ui-switch-success ui-switch-sm url-switch">
								<input type="checkbox" ng-model="googleUrl.active" ng-change="activateGoogleUrl(user.id)" ng-true-value="1" ng-false-value="0" />
								<i></i>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<button type="button" class="btn btn-default" ng-click="cancel()">{{ __('Close') }}</button>
		</div>
	</form>
</script>

<script type="text/ng-template" id="ModalAddMailbox.html">
	<form name="form" method="post" novalidate="novalidate" ng-init="getMailboxes()">
		<div class="modal-header">
			<h4 class="modal-title">{{ __("Add Mailbox") }}</h4>
		</div>

		<div class="modal-body">
			<div class="row">
				<div class="col-sm-12">
					<div class="content-loader" ng-show="request_finish">
						<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
					</div>

					<div class="col-sm-6">
						<div ng-show=" ! showAuthCodeField">
							<div class="form-group">
								<label>Mailbox Name (email):</label>
								<input type="text" class="form-control" placeholder="Email" ng-model="mailbox.email" />
							</div>

							<div class="form-group">
								<label>Mailbox password:</label>
								<input type="text" class="form-control" placeholder="Password" ng-model="mailbox.password" />
							</div>

							<div class="form-group">
								<button class="btn btn-primary btn-danger" ng-click="login()">Add mailbox</button>
							</div>
						</div>

						<div ng-show="showAuthCodeField">
							<div class="form-group">
								<label>Enter Authorization Code:</label>
								<input type="text" class="form-control" placeholder="Authorization Code" ng-model="authCode" />
							</div>

							<div class="form-group">
								<button class="btn btn-primary btn-danger" ng-click="storeTokenFile()">Login</button>
							</div>
						</div>
					</div>
					
					<div class="col-xs-6" ng-show="mailboxes && ! showAuthCodeField">
						<div class="existingAccountsWrapper">
							<div class="form-group">
								<label>Existing accounts:</label>
								<p class="existingAccountItem" ng-repeat="mailbox in mailboxes">@{{ mailbox.email }}<span class="delete-icon" title="Delete mailbox" ng-click="deleteMailbox(mailbox)"><i class="fa fa-times"></i></span></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal-footer" ng-show="action != 'assign' && action != 'assign_company'">
			<button type="button" class="btn btn-primary btn-danger" ng-click="cancel()">{{ __('Close') }}</button>
		</div>
	</form>
</script>