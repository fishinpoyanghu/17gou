/**
 * Created by Administrator on 2016/1/6.
 */

var AreaPicker = function(obj) {
  if (obj instanceof AreaPicker) return obj;
  if (!(this instanceof AreaPicker)) return new AreaPicker(obj);
};

AreaPicker.getProvinces =  function(){
  var provinces = [];
  var length = arrCity.length;
  for(var i=0;i<length;i++){
    provinces.push(arrCity[i].name);
  }
  return provinces ;
};

AreaPicker.getCity =  function(provinceName){
  var cities = [] ;
  for(var i=0;i<arrCity.length;i++){
    var pro = arrCity[i];
    if(pro.name==provinceName){
      for(var j=0;j<pro.sub.length;j++){
        cities.push(pro.sub[j].name);
      }
      break ;
    }
  }
  return cities ;
};

AreaPicker.getCounty =  function(provinceName,cityName){
  var counties = [] ;
  for(var i=0;i<arrCity.length;i++){
    var pro = arrCity[i];
    if(pro.name==provinceName){
      for(var j=0;j<pro.sub.length;j++){
        var city = pro.sub[j] ;
        if(city.name==cityName){
          for(var k=0;k<city.sub.length;k++){
            counties.push(city.sub[k].name);
          }
          break ;
        }
      }
    }
  }
  return counties ;
};
