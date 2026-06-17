

(function ($) {
  "use strict";

  // ============== Header Hide Click On Body Js Start ========
  $('.navbar-toggler.header-button').on('click', function() {
    if($('.body-overlay').hasClass('show')){
      $('.body-overlay').removeClass('show');
    }else{
      $('.body-overlay').addClass('show');
    }
  });
  $('.body-overlay').on('click', function() {
    $('.header-button').trigger('click');
  });
  // =============== Header Hide Click On Body Js End =========
  // ==========================================
  //      Start Document Ready function
  // ==========================================
  $(document).ready(function () {

    // ========================= Slick Slider Js Start ==============
    $('.testimonials-item-wrapper').slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: false,
      autoplaySpeed: 2000,
      speed: 1500,
      dots: true,
      pauseOnHover: true,
      arrows: false,
      prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-long-arrow-alt-left"></i></button>',
      nextArrow: '<button type="button" class="slick-next"><i class="fas fa-long-arrow-alt-right"></i></button>',
    });
  // ========================= Slick Slider Js End ===================
    
    // ================== Password Show Hide Js Start ==========
    $(".toggle-password").on('click', function() {
      $(this).toggleClass(" fa-eye-slash");
      var id = $(this).attr('id'); 
      var input = $('#'+id);
      if (input.attr("type") == "password") {
      input.attr("type", "text");
      input.attr("name", "password");
      } else {
      input.attr("type", "password");
      input.attr("name", "password");
      }
    });
  // =============== Password Show Hide Js End =================

      // ================== Sidebar Menu Js Start ===============
    // Sidebar Dropdown Menu Start
    $(".has-dropdown > a").click(function() {
      $(".sidebar-submenu").slideUp(200);
      if (
        $(this)
          .parent()
          .hasClass("active")
      ) {
        $(".has-dropdown").removeClass("active");
        $(this)
          .parent()
          .removeClass("active");
      } else {
        $(".has-dropdown").removeClass("active");
        $(this)
          .next(".sidebar-submenu")
          .slideDown(200);
        $(this)
          .parent()
          .addClass("active");
      }
    });
    // Sidebar Dropdown Menu End

    // Sidebar Icon & Overlay js 
      $(".dashboard-body__bar-icon").on("click", function() {
        $(".sidebar-menu").addClass('show-sidebar'); 
        $(".sidebar-overlay").addClass('show'); 
      });
      $(".sidebar-menu__close, .sidebar-overlay").on("click", function() {
        $(".sidebar-menu").removeClass('show-sidebar'); 
        $(".sidebar-overlay").removeClass('show'); 
      });
      // Sidebar Icon & Overlay js 
    // ===================== Sidebar Menu Js End =================
  
    // ========================= Odometer Counter Js End ==========
      // Odometer Counter
      $(".counterup-item").each(function () {
        $(this).isInViewport(function (status) {
          if (status === "entered") {
            for (var i = 0; i < document.querySelectorAll(".odometer").length; i++) {
              var el = document.querySelectorAll('.odometer')[i];
              el.innerHTML = el.getAttribute("data-odometer-final");
            }
          }
        });
      });
    // ========================= Counter Js End =====================

  });
  // ==========================================
  //      End Document Ready function
  // ==========================================

  // ========================= Preloader Js Start =====================
    $(window).on("load", function(){
      $('.preloader').fadeOut(); 
    })
    // ========================= Preloader Js End=====================

    // ========================= Header Sticky Js Start ==============
    $(window).on('scroll', function() {
      if ($(window).scrollTop() >= 300) {
        $('.header').addClass('fixed-header');
      }
      else {
          $('.header').removeClass('fixed-header');
      }
    }); 
    // ========================= Header Sticky Js End===================
    
    //============================ Scroll To Top Icon Js Start =========
    var btn = $('.scroll-top');

    $(window).scroll(function() {
      if ($(window).scrollTop() > 300) {
        btn.addClass('show');
      } else {
        btn.removeClass('show');
      }
    });

    btn.on('click', function(e) {
      e.preventDefault();
      $('html, body').animate({scrollTop:0}, '300');
    });
//========================= Scroll To Top Icon Js End ======================

})(jQuery);
