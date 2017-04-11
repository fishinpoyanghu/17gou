define(['avalon', 'http/http-factory', 'components/view-left-side/left', 'css!../../../css/selfInfo/member.min.css', 'page/userMes/userMes'],
    function(avalon, httpFactory) {

        var inviteMoney = avalon.define({
            $id: "inviteMoneyCtrl",
            $leftSideOpts: {
                activePage: 'inviteMoney'
            },
            data: [],
            page: 1,
            hasNextPage: true,
            isLoading: false,
            getNextPage: function() {
                if (inviteMoney.hasNextPage && !inviteMoney.isLoading) {
                    inviteMoney.page++;
                    getInviteMoney();
                } 
            },
            getPrevPage: function() {
                if (inviteMoney.page > 1 && !inviteMoney.isLoading) {
                    inviteMoney.page--;
                    getInviteMoney();
                } 
            }
        })

        function getInviteMoney() {
            inviteMoney.isLoading = true;
            httpFactory.onlyPageParams('inviteMoney',inviteMoney.page, function(re) {
                re = JSON.parse(re);
                if (re.code == 0) {
                    inviteMoney.data = re.data;
                    if (inviteMoney.data.length == 10) {
                        inviteMoney.hasNextPage = true;
                    } else {
                        inviteMoney.hasNextPage = false;
                    }
                } else {
                    layer.msg(re.msg);
                }
            }, function(err) {

            }, function() {
                inviteMoney.isLoading = false;
            })
        }


        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'inviteMoney';
                inviteMoney.page = 1;
                getInviteMoney();
            }
            $ctrl.$onBeforeUnload = function() {}

            $ctrl.$vmodels = []

        })
    })
