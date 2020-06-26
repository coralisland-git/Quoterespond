(function () {
    'use strict';

    angular.module('app').controller('MatchingsCtrl', ['$rootScope', '$scope', '$uibModal', '$window', 'request', 'langs', 'validate', MatchingsCtrl]);

    function MatchingsCtrl($rootScope, $scope, $uibModal, $window, request, langs, validate) {
        $scope.request_finish = false;
        $scope.quickSearch = '';

        $scope.init = function () {
            $scope.getMatchings();
        };

        $scope.getMatchings = function () {
            request.send('/clients/matchings', {}, function (data) {
                $scope.matchings = data;
                $scope.request_finish = true;
            }, 'get');
        };

        $scope.chooseUser = function (lead) {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'AssignUser.html',
                controller: 'ModalAssignUserCtrl',
                size: 'lg',
                resolve: {
                    items: function () {
                        return { 'lead': lead };
                    }
                }
            });

            modalInstance.result.then(function (response) {
                $scope.getMatchings();
            }, function () {

            });
        };
    };
})();

;

(function () {
    'use strict';

    angular.module('app').controller('ModalAssignUserCtrl', ['$rootScope', '$scope', '$uibModalInstance', 'request', 'validate', 'logger', 'langs', 'items', ModalAssignUserCtrl]);

    function ModalAssignUserCtrl($rootScope, $scope, $uibModalInstance, request, validate, logger, langs, items) {
        $scope.lead = items.lead;

        $scope.init = function () {
            $scope.getLiveUsers();
            $scope.getCompanyMatching();
        };

        $scope.getLiveUsers = function () {
            request.send('/users/live', {}, function (data) {
                $scope.list = data;
                $scope.request_finish = true;
            }, 'get');
        };

        $scope.getCompanyMatching = function () {
            request.send('/users/company', { 'name_string': $scope.lead.name_string }, function (data) {
                $scope.possibleMatches = data;
            }, 'post');
        };

        $scope.assignUser = function (user) {
            request.send('/homeadvisor/assign/' + user.id, { 'lead': $scope.lead }, function (data) {
                $uibModalInstance.close();
            }, 'post');
        };

        $scope.cancel = function () {
            $uibModalInstance.dismiss('cancel');
        };
    };
})();

;