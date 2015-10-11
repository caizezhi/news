$(document).ready(function(){
  $.getJSON('/admin-examine.json',{type:"examine"},function(json){
    console.log(json);
    for (var i = 0; i < json.length; i++) {
      $(".examine").append("<tr><td>"+json[i].title+"</td><td>"+json[i].author+"</td><td>2015-7-26</td><td>2015-7-27</td><td>"+json[i].click+"</td><td>原创美文</td><td>"+json[i].editor+"</td></tr>");
    }
  });
});