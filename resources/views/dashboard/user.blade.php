<div class="page page-table" data-ng-controller="DashboardCtrl" ng-init="init()">
	<div class="row">
		<div class="col-sm-12 col-md-6">
			<div class="panel panel-default">
				<div class="panel-body">
					<div>
						<form name="form_ha" novalidate="novalidate">
							<div class="form-group">
								<!-- <div class="form-group">
									<label>{{ __('Phone Number') }}</label>
									<input type="text" class="form-control" name="phone" ng-model="settings.phone" placeholder="{{ __('Phone Number') }}" />
								</div>

								<div class="form-group">
									<label>{{ __('Email') }}</label>
									<input type="text" class="form-control" name="email" ng-model="settings.email" placeholder="{{ __('Email') }}" />
								</div> -->

								<div class="form-group">
									<label>{{ __('Company Name') }}</label>
									<input type="text" class="form-control" name="company" ng-model="settings.company_name" placeholder="{{ __('Company Name') }}" />
								</div>

								<div class="form-group">
									<label>{{ __('Auto Email Response') }}</label>
									<char-set ng-model="settings.text" unique-id="'ha'" company="user.company_name"></char-set>
								</div>
							</div>

							<div class="form-group">
								<button class="btn btn-primary" type="submit" ng-click="save()">{{ __('Save') }}</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-12 col-md-6">
			<section class="panel panel-default table-dynamic table-responsive" ng-show="list.length">
				<table class="table table-middle table-leads">
					<thead>
						<tr>
							<th>
								<strong>{{ __('Leads') }}</strong>
							</th>

							<th class="lead-list-side-column text-center">
								<!-- <strong>{{ __('Pause') }}</strong>
								<strong>{{ __('Followups') }}</strong> -->
							</th>

							<th class="lead-list-side-column text-center">
								<div class="review_title">
									<strong>{{ __('Email') }}</strong>
								</div>
							</th>

							<!-- <th class="hack_column" ng-show="showHackColumn">

							</th> -->
						</tr>
					</thead>

					<tbody id="leads-container">
						<tr ng-repeat="item in list">
							<td>
								<div class="pull-left message-icon">
									
								</div>

								<div>
									<span class="small-italic">(@{{ item.created_at_string }})</span>
								</div>

								<div>
									@{{ item.name }}
								</div>
							</td>

							<td class="lead-list-side-column text-center">
								
							</td>

							<td class="lead-list-side-column text-center">
								@{{ item.email }}
							</td>
						</tr>
					</tbody>
				</table>
			</section>
		</div>
	</div>
</div>

<script type="text/ng-template" id="NewUserModal.html">
	<form name="form" method="post" novalidate="novalidate">
		<div class="modal-header">
			<h4 class="modal-title">{{ __("Welcome") }}</h4>
		</div>

		<div class="modal-body">
			<div class="row">
				<div class="col-xs-12">
					<div class="form-group text-center">
						<p>
							Thanks for signing up! We will soon email you simple instructions to get started.
							If you don’t see the email, please check spam. Thanks!
						</p>
					</div>
				</div>
			</div>
		</div>

		<div class="modal-footer">
			<button type="button" class="btn btn-default" ng-click="confirm()">{{ __('OK') }}</button>
		</div>
	</form>
</script>