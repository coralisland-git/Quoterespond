(function () {
    'use strict';

    //var stripe = Stripe('pk_live_qfYiDhjIK1fw6XPECmbLafr2');
    var stripe = Stripe('pk_test_MOvbWjrfbpMnQsTmeV4dsG1600e3JPeV6Y');
    var elements = stripe.elements();

    var style = {
        base: {
            color: '#32325d',
            lineHeight: '18px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    var card = elements.create('card', { style: style });
    card.addEventListener('change', function (event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    angular.module('app').controller('PlansCtrl', ['$rootScope', '$scope', '$uibModal', '$window', 'request', 'validate', 'langs', PlansCtrl]);

    function PlansCtrl($rootScope, $scope, $uibModal, $window, request, validate, langs) {
        $scope.request_finish = false;
        $scope.plan_name = '';
        $scope.list = [];
        $scope.stripe = {};
        $scope.companyChecked = {};
        $scope.showCardDetails = true;
        $scope.showHomeAdvisorInput = false;

        $scope.init = function () {
            $scope.get();
        };

        $scope.initPlanPage = function() {
            $scope.getPlanInfo();
            $scope.planDetailsPage();
        };

        $scope.get = function() {
        	request.send('/plans', {}, function(data) {                
                $scope.list = data;
                $scope.request_finish = true;
			}, 'get');
        };

        $scope.getPlanInfo = function() {
        	request.send('/plans/get', {}, function(data) {
                $scope.request_finish = true;
                $scope.plan_name = data.plan_name;
                if (data.stripe_id) {
                    $scope.stripe = data;
                    $scope.showCardDetails = false;
                } else {
                    $scope.stripe = {};
                    $scope.companies = data.companies;
                }
			}, 'get');
        };

        $scope.create = function(plans_id) {
            plans_id = plans_id || 0;
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'ModalPlansCreate.html',
                controller: 'ModalPlansCreateCtrl',
                resolve: {
                    items: function () {
                        return {'plan': $scope.by_id(plans_id)};
                    }
                }
            });

            modalInstance.result.then(function (response) {
               $scope.get();
            }, function () {

            });
        };

        $scope.remove = function (plans_id) {
            if (confirm(langs.get('Do you realy want to remove this Plan?'))) {
                request.send('/plans/' + plans_id, {}, function (data) {
                    $scope.get();
                }, 'delete');
            }
        };

        $scope.by_id = function(plans_id) {
            for (var k in $scope.list) {
                if ($scope.list[k].id == plans_id) {
                    return $scope.list[k];
                }
            }
            return {};
        };

        $scope.planDetailsPage = function() {
            card.mount('.card-element');
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                stripe.createToken(card).then(function (result) {
                    if (result.error) {
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                    } else {
                        $scope.request_finish = false;
                        $scope.showCardDetails = false;
                        $scope.subscribe(result.token);
                    }
                });
            });
        };

        $scope.subscribe = function(token) {
            request.send('/plans/subscribe', {'token': token.id}, function (data) {
                $scope.getPlanInfo();
                if (data) {
                    $window.location.href = '/dashboard/user/';
                } else {
                    $window.location.reload();
                }
            }, ($scope.stripe.stripe_id ? 'put' : 'post'));
        };

        $scope.cancelSubscription = function() {
            var modalInstance = $uibModal.open({
                animation: true,
                templateUrl: 'ModalCancelPlansConfirm.html',
                controller: 'ModalConfirmCancelPlanCtrl',
                resolve: {
                    items: $scope.stripe,
                }
            });

            modalInstance.result.then(function (response) {
                $scope.request_finish = false;
                if (response) {
                    $scope.getPlanInfo();
                }
            }, function () {

            });

        };

        $scope.reactivate = function(user_id) {
            $scope.request_finish = false;
            request.send('/plans/reactivate/' + user_id, {}, function (data) {
                $scope.request_finish = true;
                $scope.getPlanInfo();
            }, 'post');
        };

        $scope.getStarted = function() {
            request.send('/homeadvisor/getStarted', {}, function (data) {
                $window.location.href = '/ha/user/';
                $scope.ha.send_request = true;
            }, 'post');
        };
    };
})();

;

(function () {
    'use strict';

    angular.module('app').controller('ModalPlansCreateCtrl', ['$rootScope', '$scope', '$uibModalInstance', 'request', 'validate', 'logger', 'langs', 'items', ModalPlansCreateCtrl]);

    function ModalPlansCreateCtrl($rootScope, $scope, $uibModalInstance, request, validate, logger, langs, items) {
        $scope.plan = angular.copy(items.plan);
        if ( ! $scope.plan.id) {
            $scope.plan.interval = 'month';
        }

        $scope.save = function () {
            var error = 1;
            error *= validate.check($scope.form.name, 'Name');
            error *= validate.check($scope.form.amount, 'Amount');

            if (error) {
                request.send('/plans/' + ($scope.plan.id ? $scope.plan.id : 'save'), $scope.plan, function (data) {
                    $uibModalInstance.close(data);
                }, ($scope.plan.id ? 'post' : 'put'));
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

    angular.module('app').controller('ModalConfirmCancelPlanCtrl', ['$rootScope', '$scope', '$uibModalInstance', '$window', 'request', 'items', ModalConfirmCancelPlanCtrl]);

    function ModalConfirmCancelPlanCtrl($rootScope, $scope, $uibModalInstance, $window, request, items) {
        $scope.plan = {};
        $scope.plan = items;
        $scope.request_finish = true;
        $scope.showCancelReason = true;
        $scope.reasonTexts = false;
        $scope.reasonLeads = false;
        $scope.reasonOther = false;
        $scope.reasonPausing = false;
        $scope.reasonFinished = false;
        $scope.reasonTextsCancell = false;
        $scope.reasonTextsNotCancell = false;
        $scope.reasonLeadsQuestion = true;
        $scope.reasonTextsQuestion = true;

        $scope.clickReasonLeads = function() {
            $scope.reasonLeads = ! $scope.reasonLeads;
            $scope.showCancelReason = ! $scope.showCancelReason;
        };

        $scope.clickReasonTexts = function() {
            $scope.reasonTexts = !$scope.reasonTexts;
            $scope.showCancelReason = ! $scope.showCancelReason;
        };

        $scope.clickReasonOther = function() {
            $scope.reasonOther = ! $scope.reasonOther;
            $scope.showCancelReason = ! $scope.showCancelReason;
        };

        $scope.clickReasonLeadsPausing = function() {
            $scope.reasonPausing = ! $scope.reasonPausing;
            $scope.reasonLeadsQuestion = ! $scope.reasonLeadsQuestion;
            $scope.planNotification('pause');
            // do not cancel, email to Uri
        };

        $scope.clickReasonLeadsFinished = function() {
            $scope.reasonFinished = ! $scope.reasonFinished;
            $scope.reasonLeadsQuestion = ! $scope.reasonLeadsQuestion;
            $scope.plan.reason = 'Not buying leads anymore - Finished forever';
            setTimeout($scope.unsubscribe, 3000);
        };

        $scope.clickReasonTextsYes = function() {
            $scope.reasonTextsCancell = ! $scope.reasonTextsCancell;
            $scope.reasonTextsQuestion = ! $scope.reasonTextsQuestion;
            $scope.plan.reason = "We've tried less texts and it's still annoying customers";
            setTimeout($scope.unsubscribe, 3000);
        };

        $scope.clickReasonTextsNo = function() {
            $scope.reasonTextsNotCancell = ! $scope.reasonTextsNotCancell;
            $scope.reasonTextsQuestion = ! $scope.reasonTextsQuestion;
            $scope.planNotification('discount');
        };

        $scope.planNotification = function (reason) {
            $scope.plan = {
                reason: reason,
            };

            request.send('/plans/notifications', $scope.plan, function () {
            }, 'post');
        };

        $scope.unsubscribe = function () {
            $scope.request_finish = false;
            request.send('/plans/unsubscribe', $scope.plan, function () {
                //$scope.request_finish = true;
                $window.location.href = '/';
            }, 'post');
        };

        $scope.cancel = function () {
            $uibModalInstance.dismiss();
        };
    };
})();

;