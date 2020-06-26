<div class="page page-table" data-ng-controller="MatchingsCtrl" data-ng-init="init()">
	<h2>
		{{-- <div class="search-bar pull-right">
			<input type="text" class="form-control" ng-model="quickSearch" placeholder="{{ __('Quick Search...') }}" />
		</div> --}}

		{{ __('Leads with Unknown Users') }}
	</h2>

	<div class="content-loader" ng-show=" ! request_finish">
		<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
	</div>

	<div ng-show="request_finish">
		<div uib-alert class="alert-warning" ng-show="matchings.length == 0">
			{{ __("Nothing found.") }}
		</div>

		<section class="panel panel-default table-dynamic table-responsive " ng-show="(matchings | filter : quickSearch).length">
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
								{{ __('Source') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Email') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Phone #') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('User Name String') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Assign User') }}
							</div>
						</th>
					</tr>
				</thead>

				<tbody>
					<tr ng-repeat="lead in matchings | filter : quickSearch">
						<td>
							@{{ lead.firstname }}
						</td>

						<td>
							@{{ lead.lastname }}
						</td>

						<td>
							@{{ lead.source }}
						</td>

						<td>
							@{{ lead.email }}
						</td>

						<td>
							@{{ lead.phone }}
						</td>

						<td>
							@{{ lead.name_string }}
						</td>

						<td class="td-button text-center">
							<button class="btn btn-primary btn-danger" ng-click="chooseUser(lead)">Assign</button>
						</td>
					</tr>
				</tbody>
			</table>
		</section>
	</div>
</div>

<script type="text/ng-template" id="AssignUser.html">
	<div class="modal-header">
		<h4 class="modal-title" ng-show=" ! user.id">{{ __("Assign User") }}</h4>
	</div>

	<div class="modal-body" data-ng-init="init()">
		<h4 class="form-group">
			Name string: @{{ lead.name_string }}
		</h4>

		<hr />

		<div ng-show="possibleMatches">
			<h4 class="form-group">
				Possible Matches
			</h4>

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
								{{ __('Company Name') }}
							</div>
						</th>

						<th>
							<div class="th">
								{{ __('Assign') }}
							</div>
						</th>
					</tr>
				</thead>

				<tbody>
					<tr ng-repeat="user in possibleMatches">
						<td>
							@{{ user.firstname }}
						</td>

						<td>
							@{{ user.lastname }}
						</td>

						<td>
							@{{ user.email }}
						</td>

						<td>
							@{{ user.company_name }}
						</td>

						<td class="td-button text-center">
							<button class="btn btn-primary btn-danger" ng-click="assignUser(user)">Assign</button>
						</td>
					</tr>
				</tbody>
			</table>

			<hr />
		</div>

		<h4>
			All Users

			<div class="search-bar">
				<input type="text" class="form-control" ng-model="quickSearch" placeholder="{{ __('Quick Search...') }}" />
			</div>
		</h4>

		<div class="content-loader" ng-show=" ! request_finish">
			<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
		</div>

		<div ng-show="request_finish">
			<div uib-alert class="alert-warning" ng-show="(list | filter : quickSearch).length == 0">
				{{ __("Nothing found.") }}
			</div>

			<div class="modal_table_wrapper">
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
										{{ __('Assign') }}
									</div>
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
									@{{ user.email }}
								</td>

								<td>
									@{{ user.view_phone }}
								</td>

								<td class="td-button text-center">
									<button class="btn btn-primary btn-danger" ng-click="assignUser(user)">Assign</button>
								</td>
							</tr>
						</tbody>
					</table>
				</section>
			</div>
		</div>
	</div>
</script>