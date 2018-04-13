

( function(angular, wpNg) {
    'use strict';

    //var app = angular.module(wpNg.appName);
   var app = angular.module('electionUtilitiesApp', ["ngRoute","ngSanitize","ngAnimate","ngResource","ui.bootstrap","ui.router","infinite-scroll", "angularSpinner"]);


    app.config(function ($routeProvider, $locationProvider) {

            $locationProvider.html5Mode({
                enabled: true,
                requireBase: false,
                rewriteLinks: false
            });


            $routeProvider
                .when('/' + wpNg.config.modules.electionUtilities.electionOverviewSlug + '/', {
                    templateUrl: wpNg.config.modules.electionUtilities.partialUrl + 'election_overview.html',
                    controller: 'electionOverviewController',
                    controllerAs: 'electionOverview'
                })
                .when('/' + wpNg.config.modules.electionUtilities.ballotContestSlug + '/', {
                    templateUrl: wpNg.config.modules.electionUtilities.partialUrl + 'ballot_contest.html',
                    controller: 'ballotContestController',
                    controllerAs: 'ballotContest'
                });

        
        })
             
               
        .controller('electionOverviewController',

                ['$scope', '$route', '$routeParams', '$location', '$uibModal', 'electionOverviewService','$sce', 'usSpinnerService', '$interval',
                function ($scope, $route, $routeParams, $location, $uibModal, electionOverviewService, $sce, usSpinnerService, $interval ) {

                    // Start initilization
                    $scope.electionOverview = this;
                    $scope.electionOverview.request = {};
                    $scope.electionOverview.request.id = $scope.$parent.electionID


                    $scope.electionOverview.viewParameters = '';


                    // End initilization


                    // Start controller methods
                    $scope.today = function () {
                        $scope.dt = new Date();
                    };
                    $scope.today();

                    $scope.clearDate = function () {
                        $scope.dt = null;
                    };





                    $scope.electionOverview.fetchElectionOverview = function () {

                        if (($scope.electionOverview.loadInProgress === true)) {
                            return;
                        }

                        $scope.electionOverview.loadInProgress = true;

                        electionOverviewService.fetchElectionOverview($scope)
                            .then(function (data) {


                                if (data.data.errorData != null && data.data.errorData == "true") {
                                    $scope.electionOverview.errorMessage = data.errorMessage;
                                    $scope.electionOverview.errorContainerClass = "displayed-section";
                                    // Set loadInProgress to true in order to prevent the infinite scroll module from thrashing.
                                    //$scope.publicNoticeSearch.loadInProgress = true;
                                    console.log("Fetch of election overview from server failed.");
                                }
                                else {
                                    // If the server has no more records, it won't return any. Disable infinite scroll in that case.
                                    // Notice Factory
                                    var dataArray = [];
                                    // $scope.electionOverview.serverRecordCount = data.data.record_count ;
                                    $scope.electionOverview.title = data.data.title ;
                                    $scope.electionOverview.description = data.data.description;

                                    angular.forEach(data.data.ballot_contests, function (ballotContest) {
                                        ballotContest.visible = true;
                                        ballotContest.listAsCollapsed = true;
                                        dataArray.splice(0, 0, ballotContest );
                                    });


                                    // Default sort order is ad_startdate.
                                    if (dataArray.length > 1) {
                                        dataArray.sort(function (a, b) {
                                            return a.order - b.order;
                                        })
                                    }


                                    // $scope.electionOverview.filterNotices();

                                    $scope.electionOverview.ballotContests = dataArray;

                                    $scope.electionOverview.loadInProgress = false;


                                }

                            }, function (data) {
                                console.log('Error on notice search');
                                $scope.electionOverview.loadInProgress = false;
                            });
                    }


                    $scope.electionOverview.hideBallotContest = function() {
                        
                    }
                    // End controller methods



                    // Get election overview for display
                    $scope.electionOverview.fetchElectionOverview();


        }])
        .controller('ballotContestController',

            ['$scope', '$route', '$routeParams', '$location', '$uibModal', 'electionOverviewService','$sce', 'usSpinnerService', '$interval',
                function ($scope, $route, $routeParams, $location, $uibModal, electionOverviewService, $sce, usSpinnerService, $interval ) {

                    // Start initilization
                    $scope.ballotContest = this;
                    $scope.ballotContest.request = {};
                    $scope.ballotContest.request.id = $scope.$parent.ballotContestID;


                    $scope.ballotContest.viewParameters = '';

                    $scope.ballotContest.controlMessage = 'Choose 2 candidates below to compare side by side';
                    $scope.ballotContest.selectCount = 0;

                    // Controls display of compare and view questionnaire controls.
                    $scope.ballotContest.enableContestantNav = true;

                    $scope.ballotContest.compareModalActive = false;

                    // End initilization


                    $scope.ballotContest.fetchBallotContest = function () {

                        if (($scope.ballotContest.loadInProgress === true)) {
                            return;
                        }

                        $scope.ballotContest.loadInProgress = true;

                        electionOverviewService.fetchBallotContest($scope)
                            .then(function (data) {


                                if (data.data.errorData != null && data.data.errorData == "true") {
                                    $scope.ballotContest.errorMessage = data.errorMessage;
                                    $scope.ballotContest.errorContainerClass = "displayed-section";
                                    // Set loadInProgress to true in order to prevent the infinite scroll module from thrashing.
                                    //$scope.publicNoticeSearch.loadInProgress = true;
                                    console.log("Fetch of ballot contest from server failed.");
                                }
                                else {
                                    // If the server has no more records, it won't return any. Disable infinite scroll in that case.
                                    // Notice Factory
                                    var dataArray = [];
                                    // $scope.ballotContest.serverRecordCount = data.data.record_count ;
                                    $scope.ballotContest.title = data.data.title ;
                                    $scope.ballotContest.description = data.data.description;
                                    $scope.ballotContest.contestants = data.data.contestants;

                                    var cIndex = 0;
                                    angular.forEach( $scope.ballotContest.contestants,function( contestant ) {
                                        contestant.selected = false;
                                        contestant.listAsCollapsed = cIndex == 0 ? false : true ;
                                        cIndex++;
                                    });


                                    angular.forEach(data.data.questions, function (question) {
                                        question.visible = true;
                                        question.listAsCollapsed = true;
                                        dataArray.splice(0, 0, question );
                                    });




                                    // Default sort order
                                    // TODO: order should be determined by post date of questionnaire.
                                    if (dataArray.length > 1) {
                                        dataArray.sort(function (a, b) {
                                            return a.order - b.order;
                                        })
                                    }


                                    // $scope.ballotContest.filterNotices();

                                    $scope.ballotContest.questions = dataArray;

                                    $scope.ballotContest.loadInProgress = false;


                                }

                            }, function (data) {
                                console.log('Error on notice search');
                                $scope.ballotContest.loadInProgress = false;
                            });
                    }


                    $scope.ballotContest.hideBallotContest = function() {

                    }

                    $scope.ballotContest.detailIsCollapsed = function(contestant  ) {

                        if ( $scope.ballotContest.compareModalActive && contestant.selected ) {
                            return false;
                        }
                        return contestant.listAsCollapsed;
                    }

                    $scope.ballotContest.updateSelectorControl = function (){
                        var count = 0;
                        var message = '';
                        angular.forEach( $scope.ballotContest.contestants, function ( contestant ){
                            if ( contestant.selected ) count++;
                        });

                        if ( count < 1 ) {
                            message = 'Choose 2 candidates below to compare side by side';
                        }
                        else if ( count == 1 ) {
                            message = 'Choose 1 more candidate to compare side by side';
                        }
                        else if ( count == 2 ) {
                            message = 'Click the button to compare the 2 selected candidates';
                        }
                        else {
                            message = 'Side by side camparison is limited to 2 candidates';
                        }

                        $scope.ballotContest.controlMessage = message;
                        $scope.ballotContest.selectCount = count;

                    }

                    $scope.ballotContest.displayCompareModal = function() {

                        $scope.ballotContest.enableContestantNav = false;
                        $scope.ballotContest.compareModalActive = true;


                        var modalInstance;

                        $scope.comparelModal = modalInstance = $uibModal.open({
                            templateUrl: wpNg.config.modules.electionUtilities.partialUrl + 'candidate_compare.html',
                            scope: $scope,
                            size: 'lg',
                            windowTopClass: 'compare-modal'

                        });
                        $scope.close = function () {
                            $scope.ballotContest.enableContestantNav = true;
                            $scope.ballotContest.compareModalActive = false;
                            modalInstance.close();
                        };

                    }
                    // End controller methods


                    // Get ballot contest for display
                    $scope.ballotContest.fetchBallotContest();





        }]);

    app.factory('electionOverviewService', function ($q, $http, $location) {
        'use strict';
        var service = {};

        service.fetchElectionOverview = function ($scope) {

            var deferred = $q.defer();

            $http({
                method: 'GET',
                url: wpNg.config.ajaxUrl,
                params: {
                    'action': 'election_utilities_ajax',
                    'fn': 'fetch_election_overview',
                    'viewParameters': $scope.electionOverview.request
                }

            })
                .then(function (data) {
                    deferred.resolve(data);
                }) ,(function (data) {
                    deferred.reject('There was an error fetching election overview!');
                });
            return deferred.promise;
        };

        service.fetchBallotContest = function ($scope) {

            var deferred = $q.defer();

            $http({
                method: 'GET',
                url: wpNg.config.ajaxUrl,
                params: {
                    'action': 'election_utilities_ajax',
                    'fn': 'fetch_ballot_contest',
                    'viewParameters': $scope.ballotContest.request
                }

            })
                .then(function (data) {
                    deferred.resolve(data);
                }) ,(function (data) {
                deferred.reject('There was an error fetching ballot contest!');
            });
            return deferred.promise;
        };

        return service;

    });




    app.directive('ballotContestListing', function ($timeout) {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                electionOverview: '='
            },
            link: function (scope, elem, attrs) {

            },
            templateUrl: wpNg.config.modules.electionUtilities.partialUrl + 'ballot-contest-listing.html'

        };
    });

    app.directive('contestantDetail', [ '$sce', '$timeout', function ( $sce, $timeout) {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                contestant: '=contestant',
                ballotContest: '='
            },
            link: function (scope, elem, attrs) {
                scope.close = function() {
                    scope.contestant.listAsCollapsed = true;
                }

            },
            templateUrl: wpNg.config.modules.electionUtilities.partialUrl + 'contestant_detail.html'

        };
    }]);

})(angular, wpNg);


