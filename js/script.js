$(function(){
	
	var
	  winW = $(window).width(),
		winH = $(window).height(),
		nav = $('#mainnav ul a'),
		curPos = $(this).scrollTop();
	
	if (winW > 880){
		var headerH =20;
	}
	else{
		var headerH =60;
	}
	
	$(nav).on('click', function(){
		nav.removeClass('active');
  	var $el = $(this),
		id = $el.attr('href');
 		$('html, body').animate({
   		scrollTop: $(id).offset().top - headerH
 		}, 500);
		$(this).addClass('active');
		if (winW < 880){
			$('#menuWrap').next().slideToggle();
			$('#menuBtn').removeClass('close');
		}
 		return false;
	});
	
	$('.panel').hide();
	$('#menuWrap').toggle(function(){
		$(this).next().slideToggle();
		$('#menuBtn').toggleClass('close');
	},
	function(){
		$(this).next().slideToggle();
		$('#menuBtn').removeClass('close');
	});
    
    $( document ).ready(function( $ ) {
        $('#slider1').sliderPro({
            width: '100%',//横幅
            aspectRatio: 1.5,//縦横比
//            height '100',
            buttons: true,//ナビゲーションボタン
//            shuffle: true,//スライドのシャッフル
//            thumbnailWidth: 100,//サムネイルの横幅
//            thumbnailHeight:66,//サムネイルの縦幅
            slideDistance:0,//スライド同士の距離
//            breakpoints: {
//                799: {//表示方法を変えるサイズ
//                    width: "90%",
//                    autoHeight:true
////                    visibleSize:"100%"
//                }
//            },
//            arrows: true
        });
    });

});
