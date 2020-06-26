(function () {
    'use strict';

    angular.module('app').controller('UsersCtrl', ['$rootScope', '$scope', '$uibModal', '$window', 'request', 'langs', 'validate', UsersCtrl]);

    function UsersCtrl($rootScope, $scope, $uibModal, $window, request, langs, validate) {
		$scope.request_finish = false;
    	$scope.list = [];
		$scope.plans_list = [];
		$scope.quickSearch = '';

		$scope.initLive = function () {
			$scope.getLiveUsers();
			$scope.plans();
		};

		$scope.initCanceled = function () {
			$scope.getCanceledUsers();
			$scope.plans();
		};

		$scope.getLiveUsers = function () {
			request.send('/users/live', $scope.auth, function (data) {
				$scope.list = data;
				$scope.request_finish = true;
			}, 'get');
		};

		$scope.getCanceledUsers = function () {
			request.send('/users/canceled', $scope.auth, function (data) {
				$scope.list = data;
				$scope.request_finish = true;
			}, 'get');
		};

		$scope.plans = function () {
			request.send('/plans', false, function (data) {
				$scope.plans_list = data;
				$scope.plans_list.unshift({
					'plans_id': '0',
					'name': 'Select a Plan...'
				});
			}, 'get');
		};

		$scope.remove = function (users_id) {
            if (confirm(langs.get('Do you realy want to remove this user? It will also remove all account data'))) {
                request.send('/users/' + users_id, {}, function (data) {
					$scope.getLiveUsers();
                }, 'delete');
            }
        };

		$scope.by_id = function (users_id) {
			for (var k in $scope.list) {
				if ($scope.list[k].id == users_id) {
					return $scope.list[k];
				}
			}

			return {};
		};

		$scope.magic = function (users_id) {
			request.send('/users/' + users_id + '/magic', {}, function (data) {
				$window.location.href = "/";
            }, 'get');
		};

		$scope.pass = {};
		$scope.password = function () {
			var error = 1;
			error *= validate.check($scope.form_password.old_password, 'Old Password');
			error *= validate.check($scope.form_password.password, 'New Password');
			error *= validate.check($scope.form_password.password_confirmation, 'Password Confirmation');

			if (error) {
				request.send('/users/password', $scope.pass, function (data) {
					if (data) {
						$scope.pass.old_password = '';
						$scope.pass.password = '';
						$scope.pass.password_confirmation = '';
					}
	            });
			}
		};

		$scope.profile = function () {
			request.send('/users/profile', $scope.user);
		};

		$scope.settings = function (user) {
			var modalInstance = $uibModal.open({
				animation: true,
				templateUrl: 'ModalSettings.html',
				controller: 'ModalSettingsCtrl',
				resolve: {
					items: function () {
						return {
							'user': user,
						};
					},
				}
			});

			modalInstance.result.then(function () {
				$scope.request_finish = false;
					$scope.getLiveUsers();
					$scope.getCanceledUsers();
			}, function () {

			});
		};

		$scope.viewFullCancelReason = function (reason) {
			var modalInstance = $uibModal.open({
				animation: true,
				templateUrl: 'ModalViewCancelReason.html',
				controller: 'ModalViewCancelReasonCtrl',
				resolve: {
					items: function () {
						return {'reason': reason};
					},
				}
			});

			modalInstance.result.then(function (response) {
				//$scope.request_finish = false;
				if (response == 'downgrade' || response == 'cancel') {
					$scope.getLiveUsers();
				} else {
					$scope.getCanceledUsers();
				}
			}, function () {

			});
		};

		$scope.active = function (id) {
			request.send('/homeadvisor/setActive/' + id, {}, false, 'put');
		};

		$scope.reactivate = function (user_id) {
			$scope.request_finish = false;
			request.send('/plans/reactivate/' + user_id, {}, function (data) {
				$scope.request_finish = true;
				$scope.getCanceledUsers();
			}, 'post');
		};

		$scope.allowAccess = function(id) {
			request.send('/users/access/' + id, {}, false, 'put');
		};

		$scope.addMailbox = function () {			
			var modalInstance = $uibModal.open({
				animation: true,
				templateUrl: 'ModalAddMailbox.html',
				controller: 'ModalAddMailboxCtrl',
				resolve: {
					items: {}
				}
		    });

		    modalInstance.result.then(function (response) {

		    }, function () {

		    });
		};
    };
})();

;

(function () {
	'use strict';

	angular.module('app').controller('ModalAddMailboxCtrl', ['$rootScope', '$scope', '$uibModalInstance', '$window', '$http', 'request', 'validate', 'logger', 'langs', 'items', ModalAddMailboxCtrl]);

	function ModalAddMailboxCtrl($rootScope, $scope, $uibModalInstance, $window, $http, request, validate, logger, langs, items) {
		$scope.showAuthCodeField = false;
		$scope.mailboxName = '';
		$scope.authCode = '';
		$scope.existingAccounts = '';

		$scope.getMailboxes = function () {
			request.send('/mailboxes', {}, function (data) {
				$scope.mailboxes = data;
			}, 'get');
		};
		
		$scope.addMailbox = function () {
			request.send('/mailboxes/add', {mailbox: $scope.mailbox}, function () {
				$scope.getMailboxes();
			}, 'put');
		};
		
		$scope.deleteMailbox = function (mailbox) {
			request.send('/mailboxes/' + mailbox.id + '/' + mailbox.email, {}, function () {
				$scope.getMailboxes();
			}, 'delete');
		};
		
		$scope.checkMailboxesByTokenFiles = function () {
			request.send('/gmail/checkMailboxesByTokenFiles', {}, function (data) {
			}, 'get');
		};
		
		$scope.scanTokenDirectory = function () {
			request.send('/gmail/scanTokenDirectory', {}, function (data) {
				$scope.existingAccounts = data;
			}, 'get');
		};
		
		$scope.login = function () {
			$scope.addMailbox();
			request.send('/gmail/login', {}, function (data) {
				$window.open(data, '_blank');
				$scope.showAuthCodeField = true;
			}, 'post');
		};
		
		$scope.storeTokenFile = function () {
			request.send('/gmail/storeTokenFile', {'authCode': $scope.authCode, 'mailboxName': $scope.mailbox.email}, function (data) {
				if (data) {
					$scope.showAuthCodeField = false;
					$scope.getMailboxes();
					$scope.mailbox = {
						email: '',
						password: '',
					};
				}
			}, 'post');
		};

		$scope.cancel = function () {
			$uibModalInstance.dismiss('cancel');
		};
	};
})();

;

(function () {
	'use strict';

	angular.module('app').controller('ModalConfirmPlanCtrl', ['$rootScope', '$scope', '$uibModalInstance', '$window', 'request', 'items', ModalConfirmPlanCtrl]);

	function ModalConfirmPlanCtrl($rootScope, $scope, $uibModalInstance, $window, request, items) {
		$scope.user = items.user;
		$scope.action = items.action;
		$scope.request_finish = true;

		$scope.aprove = function() {
			if ($scope.action == 'cancel') {
				request.send('/plans/unsubscribe/' + $scope.user.id, {}, function (data) {
					$scope.request_finish = true;
					$uibModalInstance.close($scope.action);
				}, 'post');
			}

			if ($scope.action == 'reactivate') {
				request.send('/plans/reactivate/' + $scope.user.id, {}, function (data) {
					$scope.request_finish = true;
					$uibModalInstance.close($scope.action);
				}, 'post');
			}
		};

		$scope.cancel = function () {
			$uibModalInstance.dismiss();
		};
	};
})();

;

(function () {
	'use strict';

	angular.module('app').controller('ModalSettingsCtrl', ['$rootScope', '$scope', '$uibModalInstance', '$window', '$uibModal', 'request', 'items', ModalSettingsCtrl]);

	function ModalSettingsCtrl($rootScope, $scope, $uibModalInstance, $window, $uibModal, request, items) {
		
		$scope.cancel = function () {
			$uibModalInstance.close();
		};
	};
})();

;

(function () {
	'use strict';

	angular.module('app').controller('ModalViewCancelReasonCtrl', ['$rootScope', '$scope', '$uibModalInstance', '$window', 'request', 'items', ModalViewCancelReasonCtrl]);

	function ModalViewCancelReasonCtrl($rootScope, $scope, $uibModalInstance, $window, request, items) {
		$scope.reason = items.reason;

		$scope.cancel = function () {
			$uibModalInstance.dismiss();
		};
	};
})();

;