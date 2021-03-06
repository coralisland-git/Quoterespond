angular.module('app').directive('charSet', function(getShortUrl, logger, $http) {
	return {
		require: ['charSet', 'ngModel'],
		scope: {
			company: '=company',
			firstname: '=btnFirstname',
			lastname: '=btnLastname',
			hapage: '=btnHapage',
			link: '=btnLink',
			shortlink: '=btnShortlink',
			website: '=btnWebsite',
			officePhone: '=btnOfficePhone',
			lms: '=lms',
			maxFirstname: '=maxFirstname',
			maxLastname: '=maxLastname',
			id: '=uniqueId',
			result: '=ngModel',
			threeTexts: '=threeTexts',
		},
		controller: ['$scope', function CharSetCtrl($scope) {
			$scope.optout = ' Txt STOP to OptOut';
			$scope.minLms = 160 - $scope.optout.length - ($scope.company ? $scope.company.length - 2 : 0);
			$scope.firstnameTag = '[$FirstName]';
			$scope.lastnameTag = '[$LastName]';
			$scope.hapageTag = '[$JobPics]';
			$scope.linkTag = '[$Link]';
			$scope.officePhoneTag = '[$OfficePhone]';
			$scope.websiteTag = '[$Website]';
			$scope.size = 0;
			$scope.showMessageTextUrl = false;
			$scope.shortLinkMessageText = '';

			$scope.toggleUrl = function() {
				$scope.showMessageTextUrl = ! $scope.showMessageTextUrl;
			};

			$scope.insertShortLink = function(longLink) {
				$http({
					method: 'POST',
					url: '/api/v1/homeadvisor/shortlink',
					data: {'longLink': longLink}
				}).then(function (result) {
					var shortUrl = result.data.data;
					$scope.insert(shortUrl);
					$scope.shortLinkMessageText = '';
					document.getElementById('refresh').click();
				}, function (data) {
					logger.logError('Inccorect link');
				});

				/* getShortUrl.getLink(longLink, function(shortUrl) {
					if (shortUrl) {
						shortUrl = shortUrl.replace('http://', '');
						$scope.insert(shortUrl);
						$scope.shortLinkMessageText = '';
						document.getElementById('refresh').click();
					} else {
						logger.logError('Inccorect link');
					}
				}); */
			};

			$scope.charCount = function () {
				$scope.size = 0;
				if ($scope.result && $scope.result != '' && $scope.company && $scope.company != '') {
					$scope.size = $scope.result.length;
					/* if ($scope.result.indexOf($scope.firstnameTag) + 1) {
						$scope.size += $scope.firstnameTag.length;
					}

					if ($scope.result.indexOf($scope.lastnameTag) + 1) {
						$scope.size += $scope.lastnameTag.length;
					} */

					if ($scope.result.indexOf($scope.hapageTag) + 1) {
						$scope.size += 14 - $scope.hapageTag.length;
					}

					if ($scope.result.indexOf($scope.linkTag) + 1) {
						$scope.size += 14 - $scope.linkTag.length;
					}

					/* if ($scope.result.indexOf($scope.websiteTag) + 1) {
						$scope.size += 14 - $scope.websiteTag.length;
					}

					if ($scope.result.indexOf($scope.officePhoneTag) + 1) {
						$scope.size += 10 - $scope.officePhoneTag.length;
					} */
				}
			};

			$scope.maxCharCount = function () {
				var allowThreeTexts = $scope.threeTexts ? 500 : 320;
				$scope.max = ($scope.lms ? allowThreeTexts : 160) - $scope.optout.length - ($scope.company ? $scope.company.length + 2 : 0);

				if ($scope.result && $scope.result != '' && $scope.company && $scope.company != '') {
					/* if ($scope.result.indexOf($scope.firstnameTag) + 1) {
						$scope.max += $scope.maxFirstname - $scope.firstnameTag.length;
					}

					if ($scope.result.indexOf($scope.lastnameTag) + 1) {
						$scope.max += $scope.maxLastname - $scope.lastnameTag.length;
					} */

					if ($scope.result.indexOf($scope.hapageTag) + 1) {
						$scope.max -= 14 - $scope.hapageTag.length;
					}

					if ($scope.result.indexOf($scope.linkTag) + 1) {
						$scope.max -= 14 - $scope.linkTag.length;
					}

					/* if ($scope.result.indexOf($scope.websiteTag) + 1) {
						$scope.max += 14 - $scope.websiteTag.length;
					}

					if ($scope.result.indexOf($scope.officePhoneTag) + 1) {
						$scope.max += 10 - $scope.officePhoneTag.length;
					} */
				}
			};

			$scope.maxCharCount();

			$scope.$watch('result', function (newValue, oldValue) {
				$scope.charCount();
				$scope.maxCharCount();
			});

			$scope.insert = function (tag) {
				if ($scope.result.length + tag.length >= $scope.max) {
					return false;
				}

				var pos = $scope.caretPosition();
				var before = $scope.result.substr(0, pos);
				var after = $scope.result.substr(pos);
				if (before != '' && before.charAt(before.length - 1) != ' ') {
					tag = ' ' + tag;
				}

				if (after != '' && after.charAt(0) != ' ') {
					tag = tag + ' ';
				}
				$scope.result = before + tag + after;
			};

			$scope.caretPosition = function () {
				$scope.area = $('#' + $scope.id);
				return $scope.area.prop("selectionStart");
			};
		}],
		templateUrl: '/uib/template/charset/charset.html'
	};
});