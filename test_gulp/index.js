var getWechatQRCode;

//网站全局切屏交互js
var pppcarousel = {
   arrowcarousel: function(wrapid, arrowleft, arrowright, diandian, viewarea, movearea, eacharea, isauto, istouchss, time){
      var ar_cur_tapnum = 1;
      var ar_e_nums = $(wrapid).find(eacharea).length;
      var ar_e_width = $(wrapid).find(viewarea).width();
      $(wrapid).find(eacharea).width(ar_e_width);
      $(wrapid).find(movearea).width(ar_e_width*ar_e_nums);

      var iscando = true;

      var ar_issingle = ar_e_nums > 1 ? false : true;
      if(!ar_issingle) {
         var ar_e_elefirst = $(wrapid).find(eacharea).first().clone();
         var ar_e_elelast = $(wrapid).find(eacharea).last().clone();
         $(wrapid).find(movearea).prepend(ar_e_elelast);
         $(wrapid).find(movearea).append(ar_e_elefirst);
         ar_e_nums = $(wrapid).find(eacharea).length;
         $(wrapid).find(movearea).width(ar_e_width*ar_e_nums);
         $(wrapid).find(movearea).css('marginLeft',-ar_e_width*ar_cur_tapnum+'px');
      } else {
         if(arrowleft!=null) {
            $(arrowleft).hide();
         }
         if(arrowright!=null) {
            $(arrowright).hide();
         }
         if(diandian!=null) {
            $(diandian).hide();
         }
      }
      function ar_tapleft(){
         if(ar_issingle) {
            return false;
         }
         if(!iscando) {
            return false;
         }
         ++ar_cur_tapnum;
         if(ar_cur_tapnum == ar_e_nums-1){
            $(wrapid).find(movearea).animate({marginLeft: -(ar_e_width*ar_cur_tapnum)+'px'},400,function(){
               ar_cur_tapnum = 1;
               $(wrapid).find(movearea).css('marginLeft',-ar_e_width*ar_cur_tapnum+'px');
               $(wrapid).find(diandian).find('a').eq(ar_cur_tapnum-1).addClass('cur_a').siblings().removeClass('cur_a');
            });
         }  
         else {
            $(wrapid).find(movearea).animate({marginLeft: -(ar_e_width*ar_cur_tapnum)+'px'},400);
            $(wrapid).find(diandian).find('a').eq(ar_cur_tapnum-1).addClass('cur_a').siblings().removeClass('cur_a');
         }
         iscando = false;
         var givemetime = setTimeout(function(){iscando = true;},600);
      };
      function ar_tapright(){
         if(ar_issingle) {
            return false;
         }
         if(!iscando) {
            return false;
         }
         --ar_cur_tapnum;
         if(ar_cur_tapnum == 0){
            $(wrapid).find(movearea).animate({marginLeft: -(ar_e_width*ar_cur_tapnum)+'px'},400,function(){
               ar_cur_tapnum = ar_e_nums-2;
               $(wrapid).find(movearea).css('marginLeft',-ar_e_width*ar_cur_tapnum+'px');
               $(wrapid).find(diandian).find('a').eq(ar_cur_tapnum-1).addClass('cur_a').siblings().removeClass('cur_a');
            });
         }
         else {
            $(wrapid).find(movearea).animate({marginLeft: -(ar_e_width*ar_cur_tapnum)+'px'},400);
            $(wrapid).find(diandian).find('a').eq(ar_cur_tapnum-1).addClass('cur_a').siblings().removeClass('cur_a');
         }
         iscando = false;
         var givemetime = setTimeout(function(){iscando = true;},600);
      };
      function ar_gotocurtab(giveindex){
         if(ar_issingle) {
            return false;
         }
         ar_cur_tapnum = giveindex;
         $(wrapid).find(movearea).animate({marginLeft: -(ar_e_width*ar_cur_tapnum)+'px'},400);
         $(wrapid).find(diandian).find('a').eq(ar_cur_tapnum-1).addClass('cur_a').siblings().removeClass('cur_a');
      };
      if(isauto) {
         var ar_autoplay = setInterval(ar_tapleft,time);
         // $(wrapid).find(eacharea).on('mouseover',function(){
         //    clearInterval(ar_autoplay);
         // });
         // $(wrapid).find(eacharea).on('mouseleave',function(){
         //    ar_autoplay = setInterval(ar_tapleft,time);
         // });
      }
      if(arrowleft!=null) {
         $(document).on('click',arrowleft,function(){
            ar_tapright();
         });
      }
      if(arrowright!=null) {
         $(document).on('click',arrowright,function(){
            ar_tapleft();
         });
      }
      if(diandian!=null) {
         $(diandian).on('click','a',function(){
            ar_gotocurtab($(this).index()+1);
         });
      }
      if(istouchss) {
         $(wrapid).swipe({
            swipeLeft: function(){
               ar_tapleft();
            },
            swipeRight: function(){
               ar_tapright();
            },
            excludedElements:"button, input, select, textarea, .noSwipe"
         });
      }
   },
   fadecarousel: function(wrapid, eacharea, circlebox, leftclickbtn, rightclickbtn, hoverclearauto, istouchss, time){
      var jidx_sumnum = $(wrapid).find(eacharea).length-1;
      var jidx_issingle = jidx_sumnum > 0 ? false : true;
      if(jidx_issingle) {
         $(wrapid).find(circlebox).hide();
      }
      var jidx_curnum = 0;
      function jidxplay(idxnum){
         jidx_curnum = idxnum;
         $(wrapid).find(eacharea).eq(idxnum).fadeIn(600).siblings().fadeOut(600);
         $(wrapid).find(circlebox).find('a').eq(idxnum).addClass("cur_a").siblings().removeClass("cur_a");
      };
      function autojidxplay_right(){
         if(jidx_issingle) {
            return false;
         }
         if(jidx_curnum >= jidx_sumnum) {
            jidx_curnum = 0;
         }
         else {
            jidx_curnum++;
         }
         jidxplay(jidx_curnum);
      };
      function autojidxplay_left(){
         if(jidx_issingle) {
            return false;
         }
         if(jidx_curnum <= 0) {
            jidx_curnum = jidx_sumnum;
         }
         else {
            jidx_curnum--;
         }
         jidxplay(jidx_curnum);
      };
      var autojidxplayf;
      autojidxplayf = setInterval(autojidxplay_right,time);
      $(circlebox).on("click",'a',function(){
         var thecuridx = $(this).index();
         jidxplay(thecuridx);
      });
      if(leftclickbtn!=null) {
         $(document).on('click',leftclickbtn,function(){
            autojidxplay_left();
         });
      }
      if(rightclickbtn!=null) {
         $(document).on('click',rightclickbtn,function(){
            autojidxplay_right();
         });
      }
      if(!hoverclearauto){
         $(wrapid).find(eacharea).hover(function(){
            clearInterval(autojidxplayf);
         },function(){
            autojidxplayf = setInterval(autojidxplay_right,time);
         });
      }
      if(istouchss) {
         $(wrapid).swipe({
            swipeLeft: function(){
               autojidxplay_right();
            },
            swipeRight: function(){
               autojidxplay_left();
            },
            excludedElements:"button, input, select, textarea, .noSwipe"
         });
      }
   }
};

jQuery(document).ready(function($) {
   // put limit remaining for textarea input 
   $('textarea[maxcharlength]').each(function() {
       $(this).jobUtil("setInputLimit");
   });

   // re-size the table td key label field for alignment 
   $("label.key,span.labelsp.key").each(function(i, el) { beautifyTableCaption(el); });

 
   if($.isFunction($.fn.hint)) {
       // handle hint, remove blur for any input which has value set 
       $("textarea.hint, input.hint").not(function() { if($(this).attr('title') && $(this).val() == $(this).attr('title')) return true;}).hint(); 
   }

   // handle title tooltip 
   if($.isFunction($.fn.tooltip)) {      
      $(document).on("mouseenter","div[title]:not(.notooltip), span[title]:not(.notooltip), a[title]:not(.notooltip), :input[title]:not(.notooltip), img[title]:not(.notooltip), em[title]:not(.notooltip), i[title]:not(.notooltip), li[title]:not(.notooltip), label[title]:not(.notooltip)",function(){
         if($(this).attr("title").toString()) {
            $(this).attr("data-original-title",$(this).attr("title"));
            $(this).attr("title","");
         }
         $(this).tooltip({'delay': 10,container:'body'});
         $(this).triggerHandler("mouseover");
      });
   }

   if($.isFunction($.fn.corner)) {
       // corner all area by default 
       $("#contentright div.moduletable").corner("keep 5px bl br").children("h3").corner("5px tl tr"); 
       $("#contentleft div.jobdata").corner("keep 5px bl br").children("h3").corner("keep 5px tl tr");
       $("#contentleft div.userdata").corner("keep 5px bl br").children("h3").corner("keep 5px tl tr");
       $("#contentleft div.profilesection").children("h3").corner("keep 5px tl tr");
   }
   $("body").jobUtil("backtotop");
   
   $("#productintro").click(function(){
      $(this).find(".prointro_modal").show();
   });
   $(document).bind('keydown', function(event) {
      if(event.keyCode == 27) {
         $(".popupindex").hide();
         return true;
      }
   });
   $(document).bind('mousedown', function(e){
      e = $.event.fix(e);
      var otarget = $(e.target);
      if(otarget.closest(".popupindex").length>0) {
         return true;
      }
      // close all popup 
      $(".popupindex").hide();
      return true;
   });
   
   $(".iknow").click(function() {
      $(this).closest(".iknowtext").fadeOut();
      
      var expires = $(this).data('cookie');
      var name = $(this).data('cookiename');
      if(!name) 
         return; 

      if(expires && parseInt(expires) > 0)
         $.cookie(name, "yes", { expires: expires, path: '/'});
      else {
         // just session cookie 
         $.cookie(name, "yes", { path: '/'});
      }

      return false;
   });
   
   $(".eachmodalwrap").hover(function(){
      $(this).find(".index_intromore").show();
   },function(){
      $(this).find(".index_intromore").hide();
   });

   //当小屏幕时候，APP左栏变为absolute定位的时候，高度需要采用js定义
   resizeAppHeight();
   $(window).resize(function(){
      resizeAppHeight();
   });

   //全局对安排面试，复试，修改面试，发offer 提示语“简历缺少email信息，请设置发送短信”，做一个当dialog关闭，popover也立刻关闭功能。
   $(document).on('hide.bs.modal','div.modal.in',function(){
      $('.popover').hide();
   });

   //这里判定如果存在升级公告，就将正文内容向下移动一下，不存在则不动
   if( !$('.upgrade_noticebox').is(':hidden') ) {
      $('#hr_innerwraper').css('paddingTop','100px');
   };

   //V5版C端首页顶部右侧下拉菜单
   $(".tmu_hasmu").hover(function(){
      $(this).addClass('c_v5_menueach_hover');
   },function(){
      $(this).removeClass('c_v5_menueach_hover');
   });

   //V5版C端PC banner,企业,职位切屏交互
   pppcarousel.fadecarousel('.c_index_bannerarea','.cib_eachbanner','.cib_allcircle','.cib_allleftbtn a','.cib_allrightbtn a',false,false,8000);
   pppcarousel.arrowcarousel('.cici_compsbox','.cici_leftarrow','.cici_rightarrow',null,'.cici_comphidebox','.cici_complonglistbox','.cici_compsectionbox',false,false,5000);
   pppcarousel.arrowcarousel('.cijt_jobsbox','.cijt_leftarrow','.cijt_rightarrow',null,'.cijt_jobhidebox','.cijt_joblonglistbox','.cijt_jobsectionbox',false,false,5000);
   //V5版C端H5 banner切屏交互
   pppcarousel.arrowcarousel('.c_mb_bannerarea',null,null,'.cmb_allcircle','.cmb_allboxwrapper','.cmb_allbox','.cmb_eachbanner',true,true,8000);
   // c端web推荐企业
   pppcarousel.arrowcarousel('.cit_loopcont',null,null,'.cit_allcircle_box','.cit_allboxwrapper','.cit_allbox','.cit_eachloop',false,false,5000);
   //B端PC banner切屏交互
   pppcarousel.fadecarousel('.b_index_bannerarea','.bib_eachbanner','.bib_allcircle','.bib_allleftbtn a','.bib_allrightbtn a',false,false,8000);

   //新版C端首页官方微博二维码图片hover显示
   $("#cindex_showbellowimage").hover(function(){
      $(this).find(".cindex_weixin").show();
   },function(){
      $(this).find(".cindex_weixin").hide();
   });

   //C端jobseeker登录之后顶部dropdown
   $(".cr_navigation_each").hover(function(){
      var dthis = $(this);
      dthis.addClass("cr_navigation_each_hover");
      var dthat = $(this).find(".cr_navidropdown");
      if(dthat.length < 0){
         return false;
      }
      dthat.show();

      var hr_appselect_width = $(this).find(".hr_appselect_each").width();
      $(this).find(".cr_naviarrow").css({
         right: hr_appselect_width/2+3+"px"
      });

      if ($('#closenewmessagesdialog').length >0) {
         $('#setting_greentips').hide();
         var userid = $('#closenewmessagesdialog').data('userid');
         storageSet('newmessages_closer_'+userid,1);
      }
   },function(){
      var dthis = $(this);
      dthis.removeClass("cr_navigation_each_hover");
      var dthat = $(this).find(".cr_navidropdown");
      if(dthat.length < 0){
         return false;
      }
      dthat.hide();
   });

   //C端新版dialog关闭dialog全局js
   $(document).on('click','.cr_pppdialog_ui_close',function(){
      $(this).closest('.modal.cr_pppdialog_ui').modal('hide');
   });

   //新版选择职能的交互
   $(document).on('mouseover', '.pos_newzn_inner ul li', function() {
      index = $(this).index();
      $(this).addClass('cur_hover').siblings().removeClass('cur_hover');
      $('.pnzn_tabcontentbox').hide().eq(index).show();
   });

   $(document).on('mouseover', '.pnzn_twr_each', function() {
      $(this).addClass('pnzn_twr_each_hover');
   });
   
   $(document).on('mouseleave', '.pnzn_twr_each', function() {
      $(this).removeClass('pnzn_twr_each_hover');
   });

   $(document).on('mouseleave', 'a[target="_blank"][title]', function() {
      $(this).tooltip('hide');
   });
   var firstgetcode = 1;
   var gettingstatus = 0;
   /**
      USEAGE:
      getWechatQRCode({
         "binded":function(){
         },
         "watting":function(url){
            console.log(url);
         }
      });
   **/
   getWechatQRCode = function(callbacks){
      //过期时间
      var QRdata = {};
      QRdata.url = '';
      QRdata.time = 0;
      var waittingcallback = null;
      var bindedcallback = null;
      if(typeof callbacks == "object"){
         if(callbacks.hasOwnProperty('watting') && typeof(callbacks.watting)=="function"){
            waittingcallback = callbacks.watting;
         }
         if(callbacks.hasOwnProperty('binded') && typeof(callbacks.binded)=="function"){
            bindedcallback = callbacks.binded;
         }
      }
      var getRemoteQRcode = function(){
         if(gettingstatus) return;
         else gettingstatus = 1;
         $.ajax({
            type: "GET",
            url: OC.filePath('social', 'ajax', 'getwechatbindingqrcode.php'),
            data:{firstgetcode:firstgetcode,requesttoken:oc_requesttoken},
            cache: false,
            timeout: 20000,
            dataType: 'json',
            global: false,
            success: function(data){
               if (typeof data != "object"){
                  getWechatQRCode(callbacks);
                  gettingstatus = 0;
                  return;
               }
               firstgetcode = 0;
               QRdata = data;
               gettingstatus = 0;
               if(data.url){
                  if(waittingcallback&&typeof(waittingcallback)=="function") waittingcallback(QRdata.url);
                  getWechatQRCode(callbacks);
               }else{
                  if(bindedcallback&&typeof(bindedcallback)=="function") bindedcallback();
               }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
               gettingstatus = 0;
               if(waittingcallback&&typeof(waittingcallback)=="function") waittingcallback(QRdata.url);
               getWechatQRCode(callbacks);
            },
         });
      }

      getRemoteQRcode();
   }

   // 运行下面的代码可以兼容那些没有原生支持Object.key方法的JavaScript环境
   if (!Object.keys) {
      Object.keys = (function () {
         var hasOwnProperty = Object.prototype.hasOwnProperty,
             hasDontEnumBug = !({toString: null}).propertyIsEnumerable('toString'),
             dontEnums = [
               'toString',
               'toLocaleString',
               'valueOf',
               'hasOwnProperty',
               'isPrototypeOf',
               'propertyIsEnumerable',
               'constructor'
             ],
             dontEnumsLength = dontEnums.length;

         return function (obj) {
            if (typeof obj !== 'object' && typeof obj !== 'function' || obj === null) throw new TypeError('Object.keys called on non-object');

            var result = [];

            for (var prop in obj) {
               if (hasOwnProperty.call(obj, prop)) result.push(prop);
            }

            if (hasDontEnumBug) {
               for (var i=0; i < dontEnumsLength; i++) {
                  if (hasOwnProperty.call(obj, dontEnums[i])) result.push(dontEnums[i]);
               }
            }
            return result;
         }
      })()
   };
   //B端登录首页购买咨询模块上移一段距离后固定位置
   $(window).scroll(function(){
      var st = $(window).scrollTop();
      if(st > 300){
         $(".bindex_consult_phonenum").addClass("bindex_consult_fixed");
      }else{
         $(".bindex_consult_phonenum").removeClass("bindex_consult_fixed");
      }
   });

});
