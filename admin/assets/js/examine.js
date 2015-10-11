$(document).ready(function(){
  $.getJSON('/news/admin/examine/1/10/3',{type:"examine"},function(json){
    for (var i = 0; i < json.length; i++) {
      $(".allnews").append("<tr><td>"+json[i].aid+"</td><td><label class=\"checkbox-inline\"><input type=\"checkbox\" value=\""+i+"\"></label></td><td>"+json[i].title+"</td><td>"+json[i].author+"</td><td>2015-7-26</td><td>2015-7-27</td><td>"+json[i].realclick+"</td><td>"+json[i].type+"</td><td>"+json[i].editor+"</td><td>"+json[i].reviewer+"</td><td>"+json[i].reviewtime+"</td><td><button class=\"btn btn-default glyphicon glyphicon-edit\"></button><button class=\"btn btn-default glyphicon glyphicon-trash\" onclick=\"delArticle(this)\"></button></td></tr>");
    }
  });
  // modalAlt("aa","aa");
});

$(".checkall").click(function(){
  $(".allnews :checkbox").each(function(){
    $(this).attr("checked", true);
  });
});

$(".uncheckall").click(function(){
  $(".allnews :checkbox").each(function(){
    $(this).attr("checked",false);
  });
});

$(".delAll").click(function(){
  var valArr = new Array();
  $(".allnews :checkbox[checked]").each(function(i){
    valArr[i] = $(this).val();
    console.log(valArr[i]);
  });
});

function delArticle(ele) {
  if (confirm("确定删除？")) {
    $(ele).parent().parent().remove();
  }
}