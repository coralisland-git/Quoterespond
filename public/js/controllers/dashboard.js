(function () {
    'use strict';

    angular.module('app').controller('DashboardCtrl', ['$scope', '$uibModal', 'request', DashboardCtrl]);

    function DashboardCtrl($scope, $uibModal, request) {
        $scope.settings = {};
        $scope.list = [];

        $scope.init = function () {
            $scope.getSettings();
            $scope.getLeadList();
        };

        $scope.getSettings = function () {
            request.send('/settings/' + $scope.user.id, {}, function (data) {
                $scope.checkNewUser(data);
                $scope.settings = data;
                $scope.settings.phone = data.user.phone;
                $scope.settings.email = data.user.email;
                $scope.settings.company_name = data.user.company_name;
            }, 'get');
        };
        
        $scope.getLeadList = function () {            
            request.send('/clients/all', {}, function (data) {
                $scope.list = data;
            }, 'get');
        };
        
        $scope.save = function () {
            request.send('/settings/save/' + $scope.user.id, {'settings': $scope.settings}, function (data) {
                $scope.getSettings();
            }, 'put');
        };

        $scope.checkNewUser = function (settings) {
            if ( ! settings.active) {
                var modalInstance = $uibModal.open({
                    animation: true,
                    templateUrl: 'NewUserModal.html',
                    controller: 'ModalNewUserCtrl',
                    size: 'md',
                    resolve: {
                        items: function () {
                            return { 'user': $scope.user };
                        }
                    }
                });

                modalInstance.result.then(function () {

                }, function () {

                });
            }
        };
    };
})();

;

(function () {
    'use strict';

    angular.module('app').controller('ModalNewUserCtrl', ['$rootScope', '$scope', '$uibModalInstance', 'request', 'validate', 'logger', 'langs', 'items', ModalNewUserCtrl]);

    function ModalNewUserCtrl($rootScope, $scope, $uibModalInstance, request, validate, logger, langs, items) {
        $scope.user = items.user;

        $scope.confirm = function () {
            request.send('/settings/activate/' + $scope.user.id, {}, function () {
                $uibModalInstance.dismiss('cancel');
            }, 'put');
        };
    };
})();

;