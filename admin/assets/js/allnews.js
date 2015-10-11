$(document).ready(function(){
  $.getJSON('/news/admin/news/1/10/3',{type:"examine"},function(json){
    for (var i = 0; i < json.length; i++) {
      if (json[i].examine == 0) {
        json[i].examine = "未审核";
        json[i].reviewer = "--";
        json[i].reviewtime = "--";
      }else{
        json[i].examine = "已审核";
      }

      $(".allnews").append("<tr><td>"+json[i].aid+"</td><td><label class=\"checkbox-inline\"><input type=\"checkbox\" value=\""+i+"\"></label></td><td>"+json[i].title+"</td><td>"+json[i].author+"</td><td>2015-7-26</td><td>2015-7-27</td><td>"+json[i].realclick+"</td><td>"+json[i].type+"</td><td>"+json[i].editor+"</td><td>"+json[i].examine+"</td><td>"+json[i].reviewer+"</td><td>"+json[i].reviewtime+"</td><td><button class=\"btn btn-default glyphicon glyphicon-edit\"></button><button class=\"btn btn-default glyphicon glyphicon-trash\" onclick=\"delArticle(this)\"></button></td></tr>");
    }
  });
  // for (var i = 0; i < 10; i++) {
  // 	$(".allnews").append("<tr><td>12"+i+"</td><td><label class=\"checkbox-inline\"><input type=\"checkbox\" value=\""+i+"\"></label></td><td>改变不一定是痛苦，也可能是</td><td>测试者</td><td>2015-7-26</td><td>2015-7-27</td><td>1"+i+"7</td><td>校内</td><td>工大学子</td><td>已审核</td><td>工大学子</td><td>2015-10-10</td></tr>");
  // }
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