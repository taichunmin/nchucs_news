// default setting
$(document).bind("mobileinit", function(){
  $.extend(  $.mobile , {
    ajaxEnabled: false,
  });
});

// work on setting.php
$( document ).delegate("#setting", "pageinit", function() {
	$('#setting').find(':input').change(function(){
		if($(this).attr('update')!='false')
			setting_update(this);
	});
	$('#setting').find(':input').on('slidestart',function(){
		$(this).attr('update',false);
	});
	$('#setting').find(':input').on('slidestop',function(){
		$(this).removeAttr('update');
		setting_update(this);
	});
	$('#setting').bind('pagebeforechange',setting_update_all);
	$(window).unload(setting_update_all);
});
function setting_update(dom)
{
	$this = $(dom);
	var $po = {act:'setting',ts: new Date().getTime()};
	$po[$this.attr('name')] = $this.val();
	$.ajax('setting.php',{
		cache: false,
		context: dom,
		data: $po,
		dataType: 'html',
		type: 'post'
	}).error(function(){
		alert('資料更新失敗，\n可能為網路有誤。');
		history.go(0);
	});	// .success(function(){console.log('success: '+$po.ts);})
}
function setting_update_all()
{
	$('#setting :input').each(function(){setting_update(this)});
}

// work on register.php
$( document ).delegate('#register','pageinit', function(event) {
	$('#form_register').validate({
		rules: {
			email: "required email",
			pass: "required",
			pass2: {
				equalTo: "input[name=pass]"
			},
			name: "required",
			birth: "required dateISO",
			gender: "required"
		}
	});
});

// work on profile.php
$( document ).delegate('#profile','pageinit', function(event) {
	$('#form_modify').validate({
		rules: {
			pass: "required",
			pass2: {
				equalTo: "input[name=pass1]"
			},
			name: "required",
			birth: "required dateISO",
			gender: "required"
		}
	});
});