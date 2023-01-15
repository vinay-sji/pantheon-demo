/* Load jQuery.
------------------------------------------------*/
jQuery(document).ready(function ($) {

  // fulfill code
  jQuery(".fulfill-btn").click(function (e) {
    setTimeout(function(){
      location.reload(true);
    }, 1000);
    e.preventDefault();
    var $ele = $(this);

    if ($ele.hasClass('disabled')) {
      return false;
    }
    var nid = $ele.data('nid');
    console.log(nid);
    var url = '/ajax/fulfill-need';
    $.post(url, {"nid": nid}, function(resp){
    });
   
    if($ele.text()=='FulFill'){
      $ele.text("FulFilling");
    }
    if (!$(".fulfill-btn").hasClass('disabled')) {
      $(".fulfill-btn").addClass('disabled');
    }
    return false;
  });
  // fulfill support code
  jQuery(".fulfill").click(function (e) {
    setTimeout(function(){
      location.reload(true);
    }, 1000);
    e.preventDefault();
    var $el = $(this);

    if ($el.hasClass('disabled')) {
      return false;
    }

    $el.addClass('disabled');
    
    var nid = $el.data('nid');
    var url = '/ajax/fulfill-support';
    $.post(url, {"nid": nid}, function(resp){
    });

    if($el.text()=='Accept') {
      $el.text("Accepting");
      $el.attr("disabled", "disabled");
    }

    if($el.text()=='Accepting'){
      $el.addClass('disabled');
    }

    if (!$(".fulfill").hasClass('disabled')) {
      $(".fulfill").addClass('disabled');
    }
    return false;
  });

  $(".fulfill-btn").click(function(e){
    e.preventDefault();
    var $el = $(this);
    if ($el.hasClass('disabled')){
      return false;
    }
  });

  $(".fulfill").click(function(e){
    e.preventDefault();
    var $ele = $(this);
    if ($ele.hasClass('disabled')){
      return false;
    }
  });

  /**
   * to change status of need/support to fulfilled dynamically.
   */
   jQuery(".fulfilled").click(function (e) {
    e.preventDefault();
    var $ele = $(this);

    if ($ele.hasClass('disabled')) {
      return false;
    }
    var nid = $ele.data('nid');
    console.log(nid);
    var url = '/ajax/fulfilled';
    $.post(url, {"nid": nid}, function(resp){
    });
   
    if($ele.text()=='Click to Fulfilled'){
      $ele.text("Status changed");
    }
    // if (!$(".fulfill-btn").hasClass('disabled')) {
    //   $(".fulfill-btn").addClass('disabled');
    // }
    return false;
  });


  jQuery(".fulfilled-s").click(function (e) {
   e.preventDefault();
   var $ele = $(this);

   if ($ele.hasClass('disabled')) {
     return false;
   }
   var nid = $ele.data('nid');
   console.log(nid);
   var url = '/ajax/fulfilled-support';
   $.post(url, {"nid": nid}, function(resp){
   });
  
   if($ele.text()=='Click to Fulfilled'){
     $ele.text("Status changed");
   }
  //  if (!$(".fulfill-s").hasClass('disabled')) {
  //    $(".fulfill-s").addClass('disabled');
  //  }
   return false;
 });

  // jQuery(".fulfill-btn").hover(function () {
  //   $(this).attr('title', 'Click to FulFill.');
  // });
  // jQuery(".fulfill").hover(function () {
  //   $(this).attr('title', 'Click to Accept.');
  // });
 

  // Mobile menu.
  $('.mobile-menu').click(function () {
    $(this).next('.primary-menu-wrapper').toggleClass('active-menu');
  });
  $('.close-mobile-menu').click(function () {
    $(this).closest('.primary-menu-wrapper').toggleClass('active-menu');
  });

  // Full page search.
  $('.search-icon').click(function () {
    $('.search-box').css('display', 'flex');
    return false;
  });
  $('.search-box-close').click(function () {
    $('.search-box').css('display', 'none');
    return false;
  });

  //mobile menu
  $('.dropdown-arrow').click(function (e) {
    e.preventDefault();
    $(this).parent().parent().toggleClass('show');
    // $('.dropdown-arrow .submenu').toggleClass('show');
    $('.submenu').toggleClass('on');
  });

  // Scroll To Top.
  $(window).scroll(function () {
    if ($(this).scrollTop() > 80) {
      $('.scrolltop').css('display', 'flex');
    } else {
      $('.scrolltop').fadeOut('slow');
    }
  });
  $('.scrolltop').click(function () {
    $('html, body').animate( { scrollTop: 0 }, 'slow');
  });

  jQuery('.clear-all-notification').click(function () {
    jQuery('.dropdown').empty();
    });
  

// End document ready.
});

/* Function if device width is more than 767px.
------------------------------------------------*/
jQuery(window).on('load', function () {
  // Add empty space for fixed footer.
  if (jQuery(window).width() > 767) {
    var footerheight = jQuery('#footer').outerHeight(true) + 4;
    jQuery('#last-section').css('height', footerheight);
  }

// end window on load
});

jQuery(".tool").hover(
  function () {
    jQuery(this).css('cursor','pointer').attr('title', 'For any questions or to discuss any plans, please send a message to the requestor of the Support by clicking here.');
  },
);
jQuery(".toolneed").hover(
  function () {
    jQuery(this).css('cursor','pointer').attr('title', 'For any questions or to discuss any plans, please send a message to the requestor of the Need by clicking here.');
  },
);

jQuery(".fulfill").hover(
  function () {
    var $ele = jQuery(this);
    if($ele.text()=='Accept') {
      jQuery(this).css('cursor','pointer').attr('title', 'By clicking Accept you are showing the intention to provide this support.');
    }
  },
);

jQuery(".fulfill-btn").hover(
  function () {
    var $ele = jQuery(this);
    if($ele.text()=='FulFill') {
      jQuery(this).css('cursor','pointer').attr('title', 'By clicking FulFill you are showing the intention to provide this Need.');
    }
  },
);

jQuery(document).delegate('#reset','click',function(event)
{
  window.location.href = window.location.origin + window.location.pathname;
});