define(["avalon", "text!components/view-left-side/left.html"], function(avalon, template) {
 

   var widget = avalon.ui["leftSide"] = function(element, data, vmodels) {

       var options = data.leftSideOptions

       options.template = options.getTemplate(template, options);

       var vmodel = avalon.define(data.leftSideId, function(vm) {
            avalon.mix(vm, options);
            vm.$skipArray = ["template", "widgetElement"]
            vm.widgetElement = element
            vm.showTop = options.showTop;
            var inited 
            vm.$init = function() {
                if (inited) return
                inited = true;
                element.style.display = "none";
                var pageHTML = options.template;    
                element.innerHTML = pageHTML;  
                element.style.display = "block"; 
                avalon.scan(element, [vmodel].concat(vmodels))
            }
            vm.$remove = function() {  
                element.innerHTML = element.textContent = ""  
            }
       })
       return vmodel
   }


   widget.defaults = {
       activePage:'index',
       getTemplate: function(str, options) {
           return str;
       }

   }
   return avalon
})
