(function () {
    'use strict';

    angular.module('app', ['ngRoute', 'ngSanitize', 'ui.bootstrap']);
})();

(function () {
    'use strict';

    angular.module('app').controller('SignUpCtrl', ['$rootScope', '$scope', '$window', '$timeout', 'request', 'validate', 'langs', SignUpCtrl]);

    function SignUpCtrl($rootScope, $scope, $window, $timeout, request, validate, langs) {
        $scope.signUpPage = '';
        $scope.signUp = {};
        $scope.activeSelect = false;

        $scope.init = function() {

        };

        $scope.signup = function() {
            var error = 1;
            error *= validate.check($scope.form.email, 'Email');
            error *= validate.check($scope.form.password, 'Password');
            error *= validate.check($scope.form.firstname, 'Name');
            error *= validate.check($scope.form.lastname, 'Last Name');
            error *= validate.check($scope.form.type, 'Payment');
            
            $scope.signUp.plans_id = $scope.signUp.type == 'metered' ? 'yelp' : 'yelp-19';

            if (error) {
                $rootScope.request_sent = true;
                request.send('/auth/signup', $scope.signUp, function(data) {
                    if (data) {
                        $timeout(function() {
                            $window.location.href = "/plans/info";
                        }, 1000);
                    } else {
                        $rootScope.request_sent = false;
                    }
                });
            }
        };
    };
})();

;