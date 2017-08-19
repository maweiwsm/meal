/*
 * util.js  - only for cloud based system 
 */

var thirdsitenames={
   '51job'  : '前程无忧',
   'zhilian': '智联招聘',
   'jiancai': '建筑英才',
   'lagou'  : '拉勾',
   'linkedin'  : '领英',
   '58tc'   : '58同城',
   'chinahr' : '中华英才',
   'liepin' : '猎聘'
}; 

function getSyncMessage(action, type) {
   var message = "您的数据同步请求已加入到任务列表里，请稍后查看状态通知！";
   switch(action) {
      case 'fetchresumes':
      case 'fetchresume':
         message = "您的简历导入请求已加入到任务列表里，请稍后查看状态通知！";
      break;

      case 'fetchcompany':
         message  = "您的公司信息下载请求已加入到任务列表里，请稍后查看状态通知！";
      break;

      case 'renewjobs':
         message  = "您的职位刷新请求已加入到任务列表里，请稍后查看状态通知！";
      break;

      case 'fetchjobs':
      default: 
      break; 
   }

   return message; 
}

function startSync(type, data, taskaddedcb, logincb, closecb) {

   var options = {keyboard: false};
   // show pre-loading dialog 
   var maxZIndex = getMaxZIndex(".popover"); 
   var dialog = $("#accountsetdialog_"+type);
   if(dialog.length <= 0) {
      dialog = $('<div id="accountsetdialog_'+type+'" class="modal accountsetdialog"></div>').appendTo('body');
      dialog.on("show.bs.modal", function() { if(maxZIndex > 0) $(this).css('z-index', maxZIndex+1); });
   }

   // reset any hidden listening at the beginning 
   dialog.data('type', type).off("hidden.bs.modal"); 
   
   dialog.html('<div class="modal-header">' + 
               '<h3 class="title">'+thirdsitenames[type]+'</h3>' + 
               '</div>' + 
               '<div class="modal-body"><div style="text-align:center; padding: 30px 40px">' + 
               '<div class="green">正在提取登录信息...<br><br></div>'+
               '<img src="/images/3/blue-loading.gif"></div></div>');
   dialog.addClass('keepopen').modal(options).modal('show');
   // set isShown to false to prevent click-to-hide behavior 
   dialog.data('bs.modal').isShown = false; 

   $.ajax({
      type: "POST",
      url: OC.filePath('company', 'ajax', 'getsyncaccountinfo.php'),
      data: data,
      dataType: 'html',
      beforeSend: function(xhr, settings) {
         // showLoading(target, settings, null, {button:true});
      },
      success: function(result) {
         if(result == 'task-add-to-background') {
            $(document).trigger('remote_login_success', thirdsitenames[type]);
            dialog.data('bs.modal').isShown = true;
            dialog.on('hidden.bs.modal', function (e) {
               if($.isFunction(closecb)) {
                  closecb();
               }
            });
            dialog.modal('hide');
            
            // customize top message 
            if(data.syncaction == 'fetchjobs'){
               showNotifyDialog(data.autofetchon,data.vid,type);
            }else if(data.syncaction == "autofetchjobs" || data.syncaction == "autorenewjobs"){
               $('#'+data.syncaction+'_result_'+type).children('.hdbtn').removeClass('hdbtn_notok').addClass('hdbtn hdbtn_ok');
               $('#'+data.syncaction+'_result_'+type).data('loginstatus',1);
            }else{
               var message = getSyncMessage(data.syncaction ? data.syncaction : '', type);
               OC.Notification.show(message, 5000);
            }
            $("#psysnotify").trigger("showtask");
            if($.isFunction(taskaddedcb)) {
               taskaddedcb(true); 
            }
         }
         else if(result == 'hidden-task-add-to-background') {
            dialog.data('bs.modal').isShown = true;
            dialog.modal('hide');
            // customize top message 
            var message = '';
            OC.Notification.show(message, 5000);
            $("#psysnotify").trigger("showtask");
            if($.isFunction(taskaddedcb)) {
               taskaddedcb(true);
            }
         }
         else if(result == 'login-success' || result == 'trylogin-success') {
            dialog.data('bs.modal').isShown = true;
            var message = '<div class="success large">'+ (result == 'login-success' ? "登录成功！" : "登录状态良好！") + '<br><br>'; 
            dialog.find('.modal-body>div').html(message);
            setTimeout(function() {
               dialog.modal('hide');
               if($.isFunction(taskaddedcb)) {
                  taskaddedcb(); 
               }
            }, 2000); 

            if($.isFunction(closecb)) {
               dialog.on("hidden.bs.modal", function() { closecb(); });
            }
         }
         else {
            // set isShown to true back to prevent duplicate settings 
            dialog.data('bs.modal').isShown = true; 
            dialog.removeClass('keepopen').html(result).modal('show');

            $("label.key", dialog).each(function(i, el) { beautifyTableCaption(el); });
            if($.isFunction($.fn.placeholder)) {
               jQuery("input, textarea", dialog).placeholder();
            }
            if($.isFunction(logincb)) {
               logincb(); 
            }

            // register dataction if it's set 
            if(data.dataaction)
               dialog.data('dataaction', data.dataaction); 

            // register autofetchon status if it's set
            if(data.autofetchon)
               dialog.data("autofetchon", data.autofetchon); 

            // register vid if it's set
            if(data.vid)
               dialog.data("vid", data.vid); 

            // register taskaddedcb into dialog data for later recall 
            if($.isFunction(taskaddedcb))
               dialog.data('taskaddedcb', taskaddedcb); 

            dialog.on("hidden.bs.modal", function() { 
               // if dialog is not submitted but cancelled, trigger taskaddedcb 
               if(!dialog.data('submitted')) {
                  if($.isFunction(taskaddedcb)) {
                     taskaddedcb(false); 
                  }
               }

               if($.isFunction(closecb)) {
                  closecb(); 
               }
            });

         }
      }
   });
}


//if autofetchon is 1, don't ask
//if autofetchon is 0, but autofetchjobs_showdialog exists, don't ask
//otherwise ask
function showNotifyDialog(autofetchon,vid,type){
   var isautofetch = !vid || storageGet("autofetchjobs_showdialog_"+type) == vid || autofetchon == 1;
   var html = '<div class="modal-header">';
   html+= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
   html+= '<h3 id="myModalLabel">&nbsp;</h3>';
   html+= '</div>';
   html+= '<div class="modal-body">';
   html+= '<div class="wrap_content">';
   html+= '<div class="greenwords yahei clearfix">';
   html+= '<i class="pull-left" style="margin-left:40px;"></i>';
   html+= '<div class="pull-left" style="margin-left:10px;">';
   html+= '<div class="sync_nowing '+isautofetch+'">职位获取中</div>';
   if(isautofetch){
      html+= '';
   }else{
      html+= '<label class="checkbox medium gray" style="padding-top:0px; text-align:left; color:#d2871d; display:none;">'; //bug8880,取消可选每三天同步，设为默认
      html+= '<input name="noaskagain" data-syncaction="autofetchjobs" data-synctype="'+type+'" data-vid="'+vid+'" type="checkbox" checked="checked"/>每3天为您自动获取';
      html+= '</label>';
   }
   html+= '</div>';
   html+= '</div>';
   html+= '<div class="sync_redtexttips medium songti">点击顶部“消息-任务通知”可查看进度</div>';
   html+= '<div class="greybakwrap" style="margin-bottom:30px;">';
   html+= '<div class="tipgraywords medium gray">每3天为您自动获取，可在账户设置中关闭此服务</div>';
   html+= '</div>';
   html+= '</div>';
   html+= '</div>';
   $('#autofetchjobs').html(html).modal();
}


function showStatusDialog(msg, hidecallback) {
   if($('#send_status_dialog').length <= 0){
      $('body').append('<div class="modal interviewtime_dialog send_status_dialog" id="send_status_dialog" style="position:absolute;">'+
         '<div class="modal-header" style="background:none; border-bottom:0px;">'+
         '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
         '</div>'+
         '<div class="modal-body">'+
         '<div class="wrap_content">'+
         '<div class="greenwords yahei"><i></i>'+msg+'</div>'+
         '</div>'+
         '</div>'+
         '</div>'
      );
   }
   var scrolltop = $(window).scrollTop();
   $("#send_status_dialog").css({top:scrolltop+75}).modal();
   if(jQuery.isFunction(hidecallback)) {
      $('#send_status_dialog').on('hidden.bs.modal', function() {
         hidecallback(); 
      })
   }
}

//当小屏幕时候，APP左栏变为absolute定位的时候，高度需要采用js定义
function resizeAppHeight() {
   var body_hb = $(".wrapper").height();
   var body_hw = $(window).height();
   var body_useh;
   if(body_hb>=body_hw) {
     body_useh = body_hb;
   }else {
     body_useh = body_hw;
   }
   var body_w = $(window).width();
   //console.log(body_hb+":"+body_hw);
   if(body_w <= 1233) {
      if(body_useh >= 630)
         $("#apps").height(body_useh+10);
      else
         $("#apps").height(630);
   }
   else{
     $("#apps").height("100%");
   }
}

function showAutoResultDialog(target,action,source,syncaction){
   var msg = '';

   if(typeof(source) === 'string'){
      if(action == 'close')
         msg = '关闭';
      else
         msg = '开启';
      switch(source){
         case 'zhilian':
            msg+= '智联招聘';
         break;
         case '51job':
            msg+= '前程无忧';
         break;
         case 'lagou':
            msg+= '拉勾';
         break;
         case 'jiancai':
            msg+= '建筑英才';
         break;
         case 'linkedin':
            msg+= '领英';
         break;
         case '58tc':
            msg+= '58同城';
         break;
         default:
         break;
      }
   }else if(typeof(source) === 'object'){
       msg = '设置';
       var sourcetypes = [];
       $.each(source,function(index,source) {
          if(source == 'zhilian')
             sourcetypes.push('智联招聘');
          else if(source == 'jiancai')
             sourcetypes.push('建筑英才');
          else if(source == 'lagou')
             sourcetypes.push('拉勾');
          else if(source == '51job')
             sourcetypes.push('前程无忧');
          else if(source == 'linkedin')
             sourcetypes.push('领英');
          else if(source == '58tc')
             sourcetypes.push('58同城');
       });
   
       if(sourcetypes.length<=0)
          return;
       msg+=sourcetypes.join('，');
   }

   if(syncaction == 'autofetchjobs')
      msg+= '自动获取';
   else
      msg+= '自动刷新';

   if(msg.length<=0 || target.length<=0)
      return;

   var html = '<div class="modal-header">';
   html+= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
   html+= '<h3 id="myModalLabel">&nbsp;</h3>';
   html+= '</div>';
   html+= '<div class="modal-body">';
   html+= '<div class="wrap_content">';
   html+= '<div class="greenwords yahei"><i></i>您已成功'+msg+'</div>';
   if(typeof(source) === 'object'){
      html +='<div class="tipgraywords">您可在账户设置中的 <a href="'+synchronousinfo_url+'">授权管理</a> 页面关闭或启用自动刷新</div>';
   }
   html+= '</div>';
   html+= '</div>';

   target.html(html).modal();
}
