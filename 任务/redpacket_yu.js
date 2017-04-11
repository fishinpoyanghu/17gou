define(['avalon', 'http/http-factory'],
    function(avalon, httpFactory) {
        var redpacket = avalon.define({
            $id: "redpacketCtrl",
            name :'yuzz'
        })
        console.log(redpacket.name)




        

        return avalon.controller(function($ctrl) {
            $ctrl.$onRendered = function() {
                avalon.vmodels["headerEidget"].activePage = 'redpacket';
            }

            $ctrl.$onBeforeUnload = function() {}

//          $ctrl.$vmodels = []

        })
    })
