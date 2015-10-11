$("a,button").focus(function(){
  $(this).blur();
});

$(".login-box").click(function(){
  return false;
});
$(".login-btn").click(function(){
  var user  = $.trim($(".login-user").val());
  var pwd  = $.trim($(".login-pwd").val());
  if (user == "" || pwd == "") {return false;}
  $.post("datadeal.php?action=login",{user_id:user,pwd:pwd},function(resp){
    if (resp.result == "success") {
      $(".navbar-right li").html("<a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" role=\"button\" aria-haspopup=\"true\" aria-expanded=\"false\">"+resp.user_name+" <span class=\"caret\"></span></a><ul class=\"dropdown-menu\"><li><a href=\"\" onclick=\"logout()\">注销</a></li></ul>");
    }else{
      alert("登录失败，请检查用户名或密码");
    }
  },"json");
});

$(document).ready(function(){
  var url = window.location.href;
  var active = url.split("?")[1];
  if (active) {
    $("#"+active).parent().addClass("active");
  }
});

function logout(){
  if (confirm("确定要注销吗？")){
    $.post("/news/admin/logout",function(resp){
      location.href=location.href;
    },"json");
  }
  return false;
}

function modalAlt(title,body) {
	$(".modal-title").html(title);
	$(".modal-body").html(body);
	$('#Modal').modal();
}