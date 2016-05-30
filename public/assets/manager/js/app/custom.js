/********************************
Preloader
********************************/
$(window).load(function() {
  $('.loading-container').fadeOut(1000, function() {
	$(this).remove();
  });
});

$(function(){
	/*$('.dropdown-menu').click(function(event){
	  event.stopPropagation();
	});*/

	/********************************
	Toggle Aside Menu
	********************************/

	$(document).on('click', '.navbar-toggle', function(){

		$('aside.left-panel').toggleClass('collapsed');

	});

	/********************************
	Aside Navigation Menu
	********************************/

	$("aside.left-panel nav.navigation > ul > li:has(ul) > a").click(function(){

		if( $("aside.left-panel").hasClass('collapsed') == false || $(window).width() < 768 ){



		$("aside.left-panel nav.navigation > ul > li > ul").slideUp(300);
		$("aside.left-panel nav.navigation > ul > li").removeClass('active');

		if(!$(this).next().is(":visible"))
		{

			$(this).next().slideToggle(300,function(){ $("aside.left-panel:not(.collapsed)").getNiceScroll().resize(); });
			$(this).closest('li').addClass('active');
		}

		return false;

		}

	});

	/********************************
	popover
	********************************/
	if( $.isFunction($.fn.popover) ){
	$('.popover-btn').popover();
	}

	/********************************
	tooltip
	********************************/
	if( $.isFunction($.fn.tooltip) ){
	$('.tooltip-btn').tooltip()
	}

	/********************************
	NanoScroll - fancy scroll bar
	********************************/
	if( $.isFunction($.fn.niceScroll) ){
	$(".nicescroll").niceScroll({

		cursorcolor: '#9d9ea5',
		cursorborderradius : '0px'

	});
	}

	if( $.isFunction($.fn.niceScroll) ){
	$("aside.left-panel:not(.collapsed)").niceScroll({
		cursorcolor: '#8e909a',
		cursorborder: '0px solid #fff',
		cursoropacitymax: '0.5',
		cursorborderradius : '0px'
	});
	}

	/********************************
	Input Mask
	********************************/
	if( $.isFunction($.fn.inputmask) ){
		$(".inputmask").inputmask();
	}

	/********************************
	TagsInput
	********************************/
	if( $.isFunction($.fn.tagsinput) ){
		$('.tagsinput').tagsinput();
	}

	/********************************
	Chosen Select
	********************************/
	if( $.isFunction($.fn.chosen) ){
		$('.chosen-select').chosen();
        $('.chosen-select-deselect').chosen({ allow_single_deselect: true });
	}

	/********************************
	DateTime Picker
	********************************/
	if( $.isFunction($.fn.datetimepicker) ){
		$('#datetimepicker').datetimepicker();
		$('#datepicker').datetimepicker({
      pickTime: false,
      format: 'DD/MM/YYYY'
    });
		$('#timepicker').datetimepicker({pickDate: false});

		$('#datetimerangepicker1').datetimepicker();
		$('#datetimerangepicker2').datetimepicker();
		$("#datetimerangepicker1").on("dp.change",function (e) {
		   $('#datetimerangepicker2').data("DateTimePicker").setMinDate(e.date);
		});
		$("#datetimerangepicker2").on("dp.change",function (e) {
		   $('#datetimerangepicker1').data("DateTimePicker").setMaxDate(e.date);
		});
	}

	/********************************
	wysihtml5
	********************************/
	if( $.isFunction($.fn.wysihtml5) ){
		$('.wysihtml').wysihtml5();
	}

	/********************************
	wysihtml5
	********************************/
	if( $.isFunction($.fn.ckeditor) ){
	CKEDITOR.disableAutoInline = true;
	$('#ckeditor').ckeditor();
	$('.inlineckeditor').ckeditor();
	}

	/********************************
	Scroll To Top
	********************************/
	$('.scrollToTop').click(function(){
		$('html, body').animate({scrollTop : 0},800);
		return false;
	});
  /********************************
  Filter
  ********************************/
  if( $.isFunction($.fn.filtr) ){
    $('[data-search]').filtr({
      target : "[data-searchable]"
    });
  }
  /********************************
  Filter
  ********************************/
  $('[data-filter]').on("click",function(){
    $('[data-filter]').removeClass("active");
    $(this).addClass("active");

    var fl = $(this).data("filter");

    $("[data-filter-index]").each(function(){
      if($(this).data("filter-index") == fl || fl == 0 )
      {
        $(this).show();
      }
      else
      {
        $(this).hide();
      }
    });

    return false;
  });
  $('[data-filter=1]').trigger("click");

  /********************************
	CLoning
	********************************/

  $("[data-clones]").delegate( "[data-add]", "click" , function(){
    var $template = $('[data-clone-tpl]').find("[data-clone]");

    $template.clone().appendTo('[data-clones]').find("input").val("");
  });

  $("[data-clones]").delegate( "[data-remove]", "click" , function(){
         if( $(this).data("post") != undefined )
         {
           AjaxSubmit( $(this).data("post") , "POST");
         }
         else
         {
           this.closest("[data-clone]").remove();
         }
  });

  /********************************
	Charts
	********************************/

  $("[data-chart]").each(function(){
    var $this = $(this),
        action = $this.data("chart");

    $.ajax({
      url:  action,
      type: "GET",
      processData: false,
      contentType: false,
      dataType: "json",
      cache: false,
      success: function( response )
      {
        $this.dxPieChart({
          dataSource: [
            { title : response.done , val : (response.doneVal / 100) },
            { title : response.open , val : (response.openVal / 100) }
          ],
          series: {
            argumentField: 'title',
            valueField: 'val',
            type: "doughnut",
          },
          legend: {
            horizontalAlignment: "right",
            verticalAlignment: "top",
            margin: 0
          },
          tooltip: {
            enabled: true,
            format: "percent",
            precision : 0
          },
        	palette: ["#2B2F3E","#FF404B","#6bb802", "#7c37c3", "#0861ce", "#fbd005", "#4fcdfc", "#00b19d", "#ff6264"]
        });
      }
    });

  });

	/********************************
	Forms
	********************************/
  function AjaxSubmit(action , method)
  {
        $.ajax({
            url:  action,           //  Action  ( PHP SCRIPT )
            type: method,           //  Method
            processData: false,     //  Tell jQuery not to process the data
            contentType: false,     //  Tell jQuery not to set contentType
            dataType: "json",       //  Accept JSON response
            cache: false,           //  Disale Cashing
            success: function( response )
            {
              if(response.status)
              {
                $("div[role='alert']").addClass("alert-success").removeClass("alert-danger hidden")
                      .find("#title").html(response.title)
                      .parent()
                      .find("#desc").html(response.text)
              }
              else
              {
                $("div[role='alert']").addClass("alert-danger").removeClass("alert-success hidden")
                      .find("#title").text(response.title)
                      .parent()
                      .find("#desc").text(response.text);
              }
              if(response.redirect)
              {
                setTimeout(function(){
                  window.location.href = response.redirect
                },response.time);
              }
            }
        });

    return false;
  }


  $("[data-form-action]").on("click",function(){

    var $targetParent = $($(this).data("target"));

    $targetParent.find("[data-dynamic-action]").attr("action",$(this).data("form-action"));

  });
});
