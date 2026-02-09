(function ($) {
    "use strict";

    /* Cart search */
    $(".account-satting-active , .search-active").on("click", function (e) {
        e.preventDefault();
        $(this).parent().find('.account-dropdown , .search-content').slideToggle('medium');
    })

    /* Cart dropdown */
    var iconCart = $('.icon-cart');
    iconCart.on('click', function () {
        $('.shopping-cart-content').toggleClass('cart-visible');
    })

    /* Slider active */
    $('.slider-active').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        navText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>'],
        autoplayTimeout: 5000,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        item: 1,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    })

    /* Slider active 2 */
    $('.slider-active-2').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        navText: ['<i class="pe-7s-angle-left"></i>', '<i class="pe-7s-angle-right"></i>'],
        autoplayTimeout: 5000,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        item: 1,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    })


    /* Slider active */
    $('.slider-active-3').owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        autoplayTimeout: 5000,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        item: 1,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    })


    /* Instagram active */
    $('.instagram-active').owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        autoplayTimeout: 5000,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        item: 5,
        responsive: {
            0: {
                items: 2
            },
            768: {
                items: 4
            },
            1000: {
                items: 5
            }
        }
    })


    /* Collection slider active */
    $('.collection-active').owlCarousel({
        loop: true,
        nav: false,
        autoplay: true,
        autoplayTimeout: 5000,
        item: 4,
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 3
            },
            1000: {
                items: 4
            }
        }
    })


    /* Collection slider active 2 */
    $('.collection-active-2').owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        autoplayTimeout: 5000,
        item: 3,
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 3
            },
            1000: {
                items: 3
            }
        }
    })


    /* product-slider active */
    $('.product-slider-active').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        autoplayTimeout: 5000,
        item: 3,
        margin: 30,
        responsive: {
            0: {
                items: 1,
                autoplay: true,
            },
            576: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 2
            },
            1200: {
                items: 3
            }
        }
    })

    /* product-slider active 2 */
    $('.product-slider-active-2').owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        autoplayTimeout: 5000,
        item: 4,
        margin: 30,
        responsive: {
            0: {
                items: 1,
                autoplay: true,
            },
            576: {
                items: 2
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    })

    /* product-slider active 3 */
    $('.product-slider-active-3').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        navText: ['<i class="fa fa-long-arrow-left"></i>', '<i class="fa fa-long-arrow-right"></i>'],
        autoplayTimeout: 5000,
        item: 4,
        margin: 30,
        responsive: {
            0: {
                items: 1,
                autoplay: true,
            },
            576: {
                items: 2
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    })

    /* Testimonial active */
    $('.testimonial-active').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        navText: ['<i class="pe-7s-angle-left"></i>', '<i class="pe-7s-angle-right"></i>'],
        autoplayTimeout: 5000,
        item: 1,
        margin: 30,
        responsive: {
            0: {
                items: 1,
                autoplay: true,
            },
            576: {
                items: 1
            },
            768: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    })
    /* Testimonial 2 active */
    $('.testimonial-active-2').owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        navText: ['<i class="pe-7s-angle-left"></i>', '<i class="pe-7s-angle-right"></i>'],
        autoplayTimeout: 5000,
        item: 1,
        responsive: {
            0: {
                items: 1,
                autoplay: true,
            },
            576: {
                items: 1
            },
            768: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    })
    /* Testimonial 3 active */
    $('.testimonial-active-3').owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        navText: ['<i class="pe-7s-angle-left"></i>', '<i class="pe-7s-angle-right"></i>'],
        autoplayTimeout: 5000,
        item: 3,
        margin: 30,
        responsive: {
            0: {
                items: 1,
                autoplay: true,
            },
            576: {
                items: 1
            },
            768: {
                items: 2
            },
            1000: {
                items: 3
            }
        }
    })

    /* Brand logo active */
    $('.brand-logo-active').owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        autoplayTimeout: 5000,
        item: 5,
        margin: 30,
        responsive: {
            0: {
                items: 2
            },
            576: {
                items: 3
            },
            768: {
                items: 4
            },
            992: {
                items: 5
            },
            1000: {
                items: 5
            }
        }
    })

    /* Testimonials active */
    $('.testimonials-active').owlCarousel({
        loop: true,
        nav: true,
        autoplayHoverPause: true,
        autoplay: true,
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        autoplayTimeout: 5000,
        item: 3,
        margin: 30,
        responsive: {
            0: {
                items: 1,
                autoplay: true,
            },
            576: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 2
            },
            1200: {
                items: 3
            }
        }
    })

    /* Brand logo active */
    $('.brand-logo-active-2').owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        autoplayTimeout: 5000,
        item: 4,
        margin: 45,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 3
            },
            768: {
                items: 3
            },
            992: {
                items: 4
            },
            1000: {
                items: 4
            }
        }
    })


    /* Related product active */
    $('.related-product-active').owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        autoplayTimeout: 5000,
        item: 4,
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    })


    /*--- Quickview-slide-active ---*/
    $('.quickview-slide-active').owlCarousel({
        loop: true,
        navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
        margin: 15,
        smartSpeed: 1000,
        nav: true,
        dots: false,
        responsive: {
            0: {
                items: 3,
                autoplay: true,
                smartSpeed: 300
            },
            576: {
                items: 3
            },
            768: {
                items: 3
            },
            1000: {
                items: 3
            }
        }
    })


    $('.quickview-slide-active a').on('click', function () {
        $('.quickview-slide-active a').removeClass('active');
    })


    /*----------------------------
        Cart Plus Minus Button
    ------------------------------ */
    $(document).ready(function () {
        $(document).off('click.qtybutton').on('click.qtybutton', '.qtybutton', function (e) {
            e.preventDefault();
            var $button = $(this);
            var $input = $button.parent().find("input.cart-plus-minus-box");
            var oldValue = parseFloat($input.val());
            if (isNaN(oldValue)) {
                oldValue = 1;
            }

            var newVal;
            if ($button.text() === "+") {
                newVal = oldValue + 1;
            } else {
                newVal = oldValue - 1;
                if (newVal < 1) {
                    newVal = 1;
                }
            }
            $input.val(newVal);
        });
        $('.cart-plus-minus-box').each(function () {
            var inputValue = parseFloat($(this).val());
            if (isNaN(inputValue) || inputValue < 1) {
                $(this).val(1);
            }
            if (!$(this).attr('min')) {
                $(this).attr('min', '1');
            }
        });

    });


    /*--
    Menu Stick
    -----------------------------------*/
    var header = $('.sticky-bar');
    var win = $(window);
    win.on('scroll', function () {
        var scroll = win.scrollTop();
        if (scroll < 200) {
            header.removeClass('stick');
        } else {
            header.addClass('stick');
        }
    });


    /* jQuery MeanMenu */
    $('#mobile-menu-active').meanmenu({
        meanScreenWidth: "991",
        meanMenuContainer: ".mobile-menu-area .mobile-menu",
    });


    /*-----------------------------------
        Scroll zoom
    -------------------------------------- */
    window.sr = ScrollReveal({
        duration: 800,
        reset: false
    });
    sr.reveal('.scroll-zoom');


    /*-----------------------
        Shop filter active 
    ------------------------- */
    $('.filter-active a').on('click', function (e) {
        e.preventDefault();
        $('.product-filter-wrapper').slideToggle();
    })


    /*---------------------
        Price slider
    --------------------- */
    var sliderrange = $('#slider-range');
    var amountprice = $('#amount');
    $(function () {
        sliderrange.slider({
            range: true,
            min: 16,
            max: 200,
            values: [0, 99],
            slide: function (event, ui) {
                amountprice.val("Â£" + ui.values[0] + " - Â£" + ui.values[1]);
            }
        });
        amountprice.val("Â£" + sliderrange.slider("values", 0) +
            " - Â£" + sliderrange.slider("values", 1));
    });


    /* Language dropdown */
    $(".language-style a").on("click", function (e) {
        e.preventDefault();
        $(this).parent().find('.lang-car-dropdown').slideToggle('medium');
    })


    /* use style dropdown */
    $(".use-style a").on("click", function (e) {
        e.preventDefault();
        $(this).parent().find('.lang-car-dropdown').slideToggle('medium');
    })


    /*=========================
        Toggle Ativation
    ===========================*/
    function itemToggler() {
        $(".toggle-item-active").slice(0, 8).show();
        $(".item-wrapper").find(".loadMore").on('click', function (e) {
            e.preventDefault();
            $(this).parents(".item-wrapper").find(".toggle-item-active:hidden").slice(0, 4).slideDown();
            if ($(".toggle-item-active:hidden").length == 0) {
                $(this).parent('.toggle-btn').fadeOut('slow');
            }
        });
    }
    itemToggler();


    function itemToggler2() {
        $(".toggle-item-active2").slice(0, 8).show();
        $(".item-wrapper2").find(".loadMore2").on('click', function (e) {
            e.preventDefault();
            $(this).parents(".item-wrapper2").find(".toggle-item-active2:hidden").slice(0, 4).slideDown();
            if ($(".toggle-item-active2:hidden").length == 0) {
                $(this).parent('.toggle-btn2').fadeOut('slow');
            }
        });
    }
    itemToggler2();

    function itemToggler3() {
        $(".toggle-item-active3").slice(0, 8).show();
        $(".item-wrapper3").find(".loadMore3").on('click', function (e) {
            e.preventDefault();
            $(this).parents(".item-wrapper3").find(".toggle-item-active3:hidden").slice(0, 4).slideDown();
            if ($(".toggle-item-active3:hidden").length == 0) {
                $(this).parent('.toggle-btn3').fadeOut('slow');
            }
        });
    }
    itemToggler3();


    /*--------------------------
        ScrollUp
    ---------------------------- */
    $.scrollUp({
        scrollText: '<i class="fa fa-angle-double-up"></i>',
        easingType: 'linear',
        scrollSpeed: 900,
        animation: 'fade'
    });


    /*--------------------------
        Isotope
    ---------------------------- */

    $('.grid').imagesLoaded(function () {
        // init Isotope
        $('.grid').isotope({
            itemSelector: '.grid-item',
            percentPosition: true,
            layoutMode: 'masonry',
            masonry: {
                // use outer width of grid-sizer for columnWidth
                columnWidth: '.grid-sizer',
            }
        });
    });


    /*--- Clickable menu active ----*/
    const slinky = $('#menu').slinky()

    /*====== sidebarCart ======*/
    function sidebarMainmenu() {
        var menuTrigger = $('.clickable-mainmenu-active'),
            endTrigger = $('button.clickable-mainmenu-close'),
            container = $('.clickable-mainmenu');
        menuTrigger.on('click', function (e) {
            e.preventDefault();
            container.addClass('inside');
        });
        endTrigger.on('click', function () {
            container.removeClass('inside');
        });
    };
    sidebarMainmenu();


    /*=========================
        Toggle Ativation
    ===========================*/
    function itemToggler4() {
        $(".toggle-item-active4").slice(0, 6).show();
        $(".item-wrapper4").find(".loadMore4").on('click', function (e) {
            e.preventDefault();
            $(this).parents(".item-wrapper4").find(".toggle-item-active4:hidden").slice(0, 3).slideDown();
            if ($(".toggle-item-active4:hidden").length == 0) {
                $(this).parent('.toggle-btn4').fadeOut('slow');
            }
        });
    }
    itemToggler4();

    function itemToggler5() {
        $(".toggle-item-active5").slice(0, 6).show();
        $(".item-wrapper5").find(".loadMore5").on('click', function (e) {
            e.preventDefault();
            $(this).parents(".item-wrapper5").find(".toggle-item-active5:hidden").slice(0, 3).slideDown();
            if ($(".toggle-item-active5:hidden").length == 0) {
                $(this).parent('.toggle-btn5').fadeOut('slow');
            }
        });
    }
    itemToggler5();

    function itemToggler6() {
        $(".toggle-item-active6").slice(0, 6).show();
        $(".item-wrapper6").find(".loadMore6").on('click', function (e) {
            e.preventDefault();
            $(this).parents(".item-wrapper6").find(".toggle-item-active6:hidden").slice(0, 3).slideDown();
            if ($(".toggle-item-active6:hidden").length == 0) {
                $(this).parent('.toggle-btn6').fadeOut('slow');
            }
        });
    }
    itemToggler6();


    /*---------------------
        Countdown
      --------------------- */
    $('[data-countdown]').each(function () {
        var $this = $(this),
            finalDate = $(this).data('countdown');
        $this.countdown(finalDate, function (event) {
            $this.html(event.strftime('<span class="cdown day">%-D <p>Days</p></span> <span class="cdown hour">%-H <p>Hours</p></span> <span class="cdown minutes">%M <p>Minutes</p></span class="cdown second"> <span>%S <p>Second</p></span>'));
        });
    });


    /*--------------------------
        Product Zoom
    ---------------------------- */
    $(".zoompro").elevateZoom({
        gallery: "gallery",
        galleryActiveClass: "active",
        zoomWindowWidth: 300,
        zoomWindowHeight: 100,
        scrollZoom: false,
        zoomType: "inner",
        cursor: "crosshair"
    });


    /*---------------------
        Product dec slider
    --------------------- */
    $('.product-dec-slider-2').slick({
        infinite: true,
        slidesToShow: 4,
        vertical: true,
        slidesToScroll: 1,
        centerPadding: '60px',
        prevArrow: '<span class="product-dec-icon product-dec-prev"><i class="fa fa-angle-up"></i></span>',
        nextArrow: '<span class="product-dec-icon product-dec-next"><i class="fa fa-angle-down"></i></span>',
        responsive: [{
            breakpoint: 992,
            settings: {
                slidesToShow: 4,
                slidesToScroll: 1
            }
        },
        {
            breakpoint: 767,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
            }
        },
        {
            breakpoint: 479,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
            }
        }
        ]
    });


    /*---------------------
        Video popup
    --------------------- */
    $('.video-popup').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        zoom: {
            enabled: true,
        }
    });


    /*---------------------
        Sidebar active
    --------------------- */
    $('.sidebar-active').stickySidebar({
        topSpacing: 80,
        bottomSpacing: 30,
        minWidth: 767,
    });


    /* Product details slider */
    $('.product-details-slider-active').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        autoplayTimeout: 5000,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        navText: ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>'],
        item: 3,
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 3
            },
            1000: {
                items: 3
            }
        }
    })


    /*--
    Magnific Popup
    ------------------------*/
    $('.img-popup').magnificPopup({
        type: 'image',
        gallery: {
            enabled: true
        }
    });


    /*-------------------------
    Create an account toggle
    --------------------------*/
    $('.checkout-toggle2').on('click', function () {
        $('.open-toggle2').slideToggle(1000);
    });

    $('.checkout-toggle').on('click', function () {
        $('.open-toggle').slideToggle(1000);
    });


    /*---- CounterUp ----*/
    $('.count').counterUp({
        delay: 10,
        time: 1000
    });

    /* Read More Less */

    $('.moreless-button').click(function () {
        $('.moretext').slideToggle();
        if ($('.moreless-button').text() == "Read more") {
            $(this).text("Read less")
        } else {
            $(this).text("Read more")
        }
    });


    /* Blog img slide active */
    $('.blog-img-slide').owlCarousel({
        loop: true,
        nav: true,
        dots: false,
        autoplay: false,
        autoplayTimeout: 5000,
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        item: 1,
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 1
            },
            1000: {
                items: 1
            }
        }
    })


    /*====== fullpage slider active ======*/

    $('#fullpage').fullpage({
        sectionSelector: '.flone-fp-section',
        slideSelector: '.flone-fp-slide',
        navigation: true,
        responsiveWidth: 575
    });


    /*------ Wow Active ----*/
    new WOW().init();

})(jQuery);

document.addEventListener('DOMContentLoaded', function () {
    $(document).on('click', 'a[href^="tel:"]', function (e) {
        var phone = this.getAttribute('href').replace('tel:', '').trim();
        $.ajax({
            url: "/track-phone-click",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                telephone: phone,
                type: 'phone'
            },
            success: function (response) {
            },
            error: function (xhr) {
                console.error("Tracking failed:", xhr.responseJSON);
            }
        });
    });
});
function updateGrandTotal(response) {
    let subTotal = parseFloat(response.cartSubTotal.replace(',', '')) || 0;
    let vatTotal = parseFloat(response.vatTotal.replace(',', '')) || 0;
    let grandTotal = parseFloat(response.cartTotalPrice.replace(',', '')) || 0;

    const shippingPostcode = response.shippingPostcode || '';
    const shippingPricePerJob = parseFloat(response.shippingPricePerJob.replace(',', '')) || 0;
    const shippingPricePerTyre = parseFloat(response.shippingPricePerTyre.replace(',', '')) || 0;
    const shippingVAT = parseFloat(response.shippingVAT.replace(',', '')) || 0;

    $('#totalbill h4 #sub-total').text('£' + subTotal.toFixed(2));
    $('#totalbill h4 #shippingPrice').text('£' + (shippingPricePerJob + shippingPricePerTyre).toFixed(2));
    $('#totalbill h4 #vat-total').text('£' + vatTotal.toFixed(2));
    $('#totalbill h4 #grand-total').text('£' + grandTotal.toFixed(2));
    $('.shopping-cart-total h4 #sub-total').text('£' + subTotal.toFixed(2));
    $('.shopping-cart-total h4 #shippingPrice').text('£' + (shippingPricePerJob + shippingPricePerTyre).toFixed(2));
    $('.shopping-cart-total h4 #vat-total').text('£' + vatTotal.toFixed(2));
    $('.shopping-cart-total h4 #grand-total').text('£' + grandTotal.toFixed(2));
    $('.count-style').text(response.remainingItems);
}
document.addEventListener('DOMContentLoaded', function () {
    $(document).on('click', '.update-cart', function () {
        const id = $(this).data('id');
        const action = $(this).data('action');
        $.ajax({
            url: "/cart/update",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: id,
                action: action
            },
            success: function (response) {
                if (response.success) {
                    let row = $('button[data-id="' + id + '"]').closest('tr');
                    let quantityElement = row.find('.quantity');
                    let totalElement = row.find('.total');
                    let price = parseFloat(row.find('.price .amount').text().replace('£', '')) || 0;
                    let taxClassId = row.data('tax-class-id') || 0;
                    let currentQuantity = parseInt(quantityElement.text());
                    if (action === 'increase') {
                        currentQuantity++;
                    } else if (action === 'decrease' && currentQuantity > 1) {
                        currentQuantity--;
                    }
                    quantityElement.text(currentQuantity);

                    let vatRate = taxClassId == 9 ? 1.2 : 1;
                    let itemPriceWithVAT = price * vatRate;
                    let itemTotal = price * currentQuantity;

                    totalElement.text('£' + itemTotal.toFixed(2));

                    updateGrandTotal(response);
                } else {
                    let row = $('tr[data-id="cart-item-' + id + '"]');
                    let errorMessageElement = row.find('#stockavail');
                    errorMessageElement.text(response.message || 'Failed to update the cart.');
                    errorMessageElement.show();
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', status, error);
            }
        });
    });

    $(document).on('click', '.delete-item', function () {
        const id = $(this).data('id');

        $.ajax({
            url: "/cart/delete",
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { id: id },
            success: function (response) {
                if (response.success) {
                    $('#cart-item-' + id).remove();
                    $('a[data-id="' + id + '"]').closest('tr').remove();
                    updateCartUI(response);
                    updateGrandTotal(response);
                    const serviceBtn = document.querySelector(
                        `.garage-checkout-services-component .add-to-cart-btn[data-id="${id}"]`
                    );

                    if (serviceBtn) {
                        serviceBtn.textContent = 'Add to Cart';
                        serviceBtn.classList.remove('added');
                        serviceBtn.disabled = false;
                    }

                    if ($('#cart-items-list').children().length === 0 || response.remainingItems === 0) {
                        window.location.href = "/";
                    }
                } else {
                    alert(response.message || 'Failed to delete the item.');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', { status, error, xhr });
            }
        });
    });

});

document.addEventListener("DOMContentLoaded", function () {
    const vrmSearchForm = document.getElementById('vrmSearchForm');
    if (vrmSearchForm) {
        vrmSearchForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const vrm = document.getElementById('reg_number').value.trim();
            const fittingType = document.getElementById('order_type_reg').value;
            const postcode = document.getElementById('postcode').value.trim();
            const modalContent = document.getElementById('vehicleDataContent');
            const tyreSizeSelection = document.getElementById('tyreSizeSelection');
            const tyreSizeSelect = document.getElementById('tyreSizeSelect');
            const continueButton = document.getElementById('continueButton');
            modalContent.innerHTML = '<p>Loading...</p>';
            tyreSizeSelection.style.display = 'none';
            continueButton.style.display = 'none';

            try {
                // Fetch VRM details from the API
                const response = await fetch(`${window.location.origin}/vehicle-data?vrm=${vrm}`, {
                    headers: {
                        'X-Request-Token': 'wrt9fe40d38302123a634c305ef580',
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success && result.data && result.data.RapidVehicleDetails) {
                    const vehicleDetails = result.data.RapidVehicleDetails || {};
                    const tyreDetails = result.data.TyreDetails?.TyreDetailsList || [];

                    // Extract unique tyre sizes
                    const tyreSizes = new Set();
                    tyreDetails.forEach(record => {
                        if (record.Front?.Tyre?.SizeDescription) {
                            tyreSizes.add(record.Front.Tyre.SizeDescription);
                        }
                        if (record.Rear?.Tyre?.SizeDescription) {
                            tyreSizes.add(record.Rear.Tyre.SizeDescription);
                        }
                    });

                    // Display vehicle details in the modal
                    modalContent.innerHTML = `
                    <div class="rounded-3">
                    <div class="your_vehicle_result d-flex justify-content-between align-items-center">
                        <div class="vrm_plate d-flex align-items-center">
                            <span class="text-uppercase">${vrm}</span>
                        </div>
                       <div id="brandImageContainer"></div>
                    </div>
                    <div class="your_vehicle_data mt-4 d-flex flex-wrap gap-4">
                        <div class="item">
                            Model
                            <span>${vehicleDetails.Model || 'N/A'}</span>
                        </div>
                        <div class="item">
                            Year
                            <span>${vehicleDetails.DateOfFirstRegistration ? new Date(vehicleDetails.DateOfFirstRegistration).getFullYear() : 'N/A'}</span>
                        </div>
                        <div class="item">
                            Engine Capacity
                            <span>${vehicleDetails.EngineCapacityCc || 'N/A'} CC</span>
                        </div>
                        <div class="item">
                            Fuel
                            <span>${vehicleDetails.FuelType || 'N/A'}</span>
                        </div>
                    </div>
                </div>
                
            `;
                    const brandImage = getBrandImage(vehicleDetails.Make);
                    document.getElementById('brandImageContainer').appendChild(brandImage);

                    // Extract tyre data with indices
                    const frontTyres = [];
                    const rearTyres = [];

                    tyreDetails.forEach(record => {
                        if (record.Front?.Tyre?.SizeDescription) {
                            frontTyres.push({
                                size: record.Front.Tyre.SizeDescription,
                                loadIndex: record.Front.Tyre.LoadIndex || 'N/A',
                                speedIndex: record.Front.Tyre.SpeedIndex || 'N/A'
                            });
                        }
                        if (record.Rear?.Tyre?.SizeDescription) {
                            rearTyres.push({
                                size: record.Rear.Tyre.SizeDescription,
                                loadIndex: record.Rear.Tyre.LoadIndex || 'N/A',
                                speedIndex: record.Rear.Tyre.SpeedIndex || 'N/A'
                            });
                        }
                    });

                    // Helper: create display string with indices
                    function formatTyreLabel(tyre) {
                        return `${tyre.size} (${tyre.loadIndex} ${tyre.speedIndex})`;
                    }

                    const frontsEqualRears =
                        frontTyres.length === rearTyres.length &&
                        frontTyres.every((front, i) => {
                            const rear = rearTyres[i];
                            return front.size === rear?.size &&
                                front.loadIndex === rear?.loadIndex &&
                                front.speedIndex === rear?.speedIndex;
                        });

                    if (frontTyres.length > 0 || rearTyres.length > 0) {
                        tyreSizeSelection.style.display = 'block';
                        tyreSizeSelect.innerHTML = '';

                        let tyreSizeHtml = '<div class="row">';

                        if (frontsEqualRears && frontTyres.length > 0) {
                            // Show combined section
                            tyreSizeHtml += `
            <div class="col-md-12">
                <h5>Recommended Tyre Size (Front & Rear)</h5>`;
                            frontTyres.forEach((tyre, idx) => {
                                const id = `tyre_${idx}`;
                                tyreSizeHtml += `
                <div class="radiobtn">
                    <input class="form-check-input tyre-size-radio" type="radio" name="tyreSizeRadio" id="${id}" value="${tyre.size}" data-load="${tyre.loadIndex}" data-speed="${tyre.speedIndex}">
                    <label class="form-check-label" for="${id}">
                        ${formatTyreLabel(tyre)}
                    </label>
                </div>`;
                            });
                            tyreSizeHtml += `</div>`;
                        } else {
                            // Show separate sections
                            if (frontTyres.length > 0) {
                                tyreSizeHtml += `
                <div class="col-md-6">
                    <h6><strong>Front Tyre Sizes</strong></h6>`;
                                frontTyres.forEach((tyre, idx) => {
                                    const id = `front_${idx}`;
                                    tyreSizeHtml += `
                    <div class="radiobtn">
                        <input class="form-check-input tyre-size-radio" type="radio" name="tyreSizeRadio" id="${id}" value="${tyre.size}" data-load="${tyre.loadIndex}" data-speed="${tyre.speedIndex}">
                        <label class="form-check-label" for="${id}">
                            ${formatTyreLabel(tyre)}
                        </label>
                    </div>`;
                                });
                                tyreSizeHtml += `</div>`;
                            }

                            if (rearTyres.length > 0) {
                                tyreSizeHtml += `
                <div class="col-md-6">
                    <h6><strong>Rear Tyre Sizes</strong></h6>`;
                                rearTyres.forEach((tyre, idx) => {
                                    const id = `rear_${idx}`;
                                    tyreSizeHtml += `
                    <div class="radiobtn">
                        <input class="form-check-input tyre-size-radio" type="radio" name="tyreSizeRadio" id="${id}" value="${tyre.size}" data-load="${tyre.loadIndex}" data-speed="${tyre.speedIndex}">
                        <label class="form-check-label" for="${id}">
                            ${formatTyreLabel(tyre)}
                        </label>
                    </div>`;
                                });
                                tyreSizeHtml += `</div>`;
                            }
                        }

                        tyreSizeHtml += `</div>`;
                        tyreSizeSelect.innerHTML = tyreSizeHtml;
                        document.querySelectorAll('.tyre-size-radio').forEach(radio => {
                            radio.addEventListener('change', function () {
                                const selectedTyreSize = this.value;
                                const loadIndex = this.getAttribute('data-load');
                                const speedIndex = this.getAttribute('data-speed');
                                const VehicleClass = vehicleDetails.VehicleClass.toLowerCase();
                                if (selectedTyreSize) {
                                    continueButton.style.display = 'inline-block';
                                    continueButton.onclick = function () {
                                        const widthProfile = selectedTyreSize.split('R')[0].split('/');
                                        const width = widthProfile[0];
                                        const profile = widthProfile[1];
                                        const diameter = selectedTyreSize.split('R')[1];
                                        const searchParams = new URLSearchParams({
                                            vehicle_type: VehicleClass,
                                            width: width,
                                            profile: profile,
                                            diameter: diameter,
                                            fitting_type: fittingType,
                                            postcode: postcode
                                        });

                                        if (loadIndex && loadIndex !== 'N/A') {
                                            searchParams.set('load_index', loadIndex);
                                        }
                                        if (speedIndex && speedIndex !== 'N/A') {
                                            searchParams.set('speed_index', speedIndex);
                                        }

                                        const url = `${window.location.origin}/tyreslist?${searchParams.toString()}`;
                                        const vehicleData = {
                                            regNumber: vrm,
                                            make: vehicleDetails.Make,
                                            model: vehicleDetails.Model,
                                            year: vehicleDetails.DateOfFirstRegistration ? new Date(vehicleDetails.DateOfFirstRegistration).getFullYear() : 'N/A',
                                            engine: vehicleDetails.EngineCapacityCc || 'N/A',
                                            tyreSizes: Array.from(tyreSizes),
                                        };

                                        storeVehicleData(vehicleData);

                                        const modal = bootstrap.Modal.getInstance(document.getElementById('vehicleDataModal'));
                                        modal.hide();

                                        // Redirect to listing page with ALL parameters in URL
                                        window.location.href = url;
                                    };
                                } else {
                                    continueButton.style.display = 'none';
                                }
                            });
                        });
                    } else {
                        tyreSizeSelection.style.display = 'none';
                        modalContent.innerHTML += '<p>No tyre sizes found for this vehicle.</p>';
                    }

                    // Store vehicle data in session (without fitting_type and postcode)
                    const vehicleData = {
                        regNumber: vrm,
                        make: vehicleDetails.Make,
                        model: vehicleDetails.Model,
                        year: vehicleDetails.DateOfFirstRegistration ? new Date(vehicleDetails.DateOfFirstRegistration).getFullYear() : 'N/A',
                        engine: vehicleDetails.EngineCapacityCc || 'N/A',
                        tyreSizes: Array.from(tyreSizes),
                    };

                    storeVehicleData(vehicleData);

                } else {
                    modalContent.innerHTML = `<p>Error: ${result.error || 'Unable to load vehicle data.'}</p>`;
                }

                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('vehicleDataModal'));
                modal.show();

            } catch (error) {
                console.error('Fetch Error:', error.message);
                modalContent.innerHTML = `<p class="text-red">Fetch Error: ${error.message}</p>`;
                const modal = new bootstrap.Modal(document.getElementById('vehicleDataModal'));
                modal.show();
            }
        });
    }

    // Handle Tyre Size Search Form
    const tyreSearchForm = document.getElementById('tyreSearchForm');
    if (tyreSearchForm) {
        tyreSearchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const vehicleType = document.getElementById('vehicle_type').value;
            const width = document.getElementById('car_width').value;
            const profile = document.getElementById('car_profile').value;
            const diameter = document.getElementById('car_diameter').value;
            const fittingType = document.getElementById('order_type').value;
            const postcode = document.querySelector('#search-by-size #postcode').value;

            // Build URL with parameters
            const searchParams = new URLSearchParams({
                width: width,
                profile: profile,
                diameter: diameter,
                fitting_type: fittingType,
                vehicle_type: vehicleType,
                postcode: postcode
            });

            // Store search parameters in session
            const searchData = {
                width: width,
                profile: profile,
                diameter: diameter,
            };

            window.location.href = `${this.action}?${searchParams.toString()}`;
        });
    }

    // Function to store vehicle data in session
    async function storeVehicleData(vehicleData) {
        try {
            const sessionResponse = await fetch('/store-vehicle-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify(vehicleData),
            });

            const sessionResult = await sessionResponse.json();
            if (!sessionResponse.ok || !sessionResult.success) {
                console.error('Failed to store vehicle data:', sessionResult.message || 'Unknown error');
            }
        } catch (error) {
            console.error('Error storing vehicle data:', error.message);
        }
    }


    function getBrandImage(make) {
        const makeSlug = make.toLowerCase() + ".webp";
        const localUrl = window.carImageConfig.localPath + "/" + makeSlug;
        const cdnUrl = window.carImageConfig.cdnBase + makeSlug;
        const fallback = window.carImageConfig.defaultImage;

        const img = document.createElement('img');
        img.className = "default-img";
        img.alt = "Brand Logo";
        img.height = 50;
        img.src = cdnUrl;

        img.onerror = function () {
            this.onerror = null;
            this.src = localUrl;
            this.onerror = function () {
                this.src = fallback;
            };
        };

        return img;
    }
});
// Prevent the form's default submit behavior
document.addEventListener("DOMContentLoaded", function () {
    const vrmSeviceForm = document.getElementById('vrmSeviceForm');
    if (vrmSeviceForm) {
        vrmSeviceForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const vrm = document.getElementById('reg_service_number').value.trim();
            const modalContent = document.getElementById('serviceVehicleDataContent');
            const serviceContinueButton = document.getElementById('serviceContinueButton');

            modalContent.innerHTML = '<p>Loading...</p>'; // Initial loading message
            serviceContinueButton.style.display = 'none'; // Hide continue button initially

            try {
                // Use fetch to send the vrm to the controller
                const response = await fetch(`/vehicle-data?vrm=${vrm}&packageType=TyreData`, {
                    method: 'GET', // Ensure it's a GET request
                    headers: {
                        'X-Request-Token': 'wrt9fe40d38302123a634c305ef580',
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok && result.success && result.data && result.data.RapidVehicleDetails) {
                    const vehicleDetails = result.data.RapidVehicleDetails || {};

                    // Update the modal with vehicle details
                    modalContent.innerHTML = `
                     <div class="bg-light py-4 px-4 mb-3 border rounded">
                        <div class="your_vehicle_result d-flex justify-content-between align-items-center">
                            <div class="vrm_plate d-flex align-items-center">
                                <img src="frontend/themes/default/img/icon-img/reg_icon.png" alt="uk icon" width="auto" height="35" loading="lazy">
                                <span class="ms-2 text-uppercase">${vehicleDetails.Vrm.toUpperCase()}</span>
                            </div>
                           <div id="brandImageContainer"></div>
                        </div>
                        <div class="your_vehicle_data mt-4 d-flex flex-wrap gap-3">
                            <div class="item">
                                Model
                                <span>${vehicleDetails.Model || 'N/A'}</span>
                            </div>
                            <div class="item">
                                Year
                                <span>${vehicleDetails.DateOfFirstRegistration ? new Date(vehicleDetails.DateOfFirstRegistration).getFullYear() : 'N/A'}</span>
                            </div>
                            <div class="item">
                                Engine Capacity
                                <span>${vehicleDetails.EngineCapacityCc || 'N/A'}</span>
                            </div>
                            <div class="item">
                                Fuel
                                <span> ${vehicleDetails.FuelType || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                `;

                    serviceContinueButton.style.display = 'inline-block'; // Show the continue button
                    const brandImage = getBrandImage(vehicleDetails.Make);
                    document.getElementById('brandImageContainer').appendChild(brandImage);
                    function getBrandImage(make) {
                        // console.log(make);
                        const makeSlug = make.toLowerCase() + ".webp";
                        const localUrl = window.carImageConfig.localPath + "/" + makeSlug;
                        const cdnUrl = window.carImageConfig.cdnBase + makeSlug;
                        const fallback = window.carImageConfig.defaultImage;

                        const img = document.createElement('img');
                        img.className = "default-img";
                        img.alt = "Brand Logo";
                        // img.width = "auto";
                        img.height = 50;
                        img.src = cdnUrl;

                        img.onerror = function () {
                            this.onerror = null;
                            this.src = localUrl;
                            this.onerror = function () {
                                this.src = fallback;
                            };
                        };

                        return img;
                    }

                    // Add functionality to the continue button
                    serviceContinueButton.onclick = async function () {
                        const vehicleData = {
                            make: vehicleDetails.Make || 'N/A',
                            model: vehicleDetails.Model || 'N/A',
                            year: vehicleDetails.DateOfFirstRegistration
                                ? new Date(vehicleDetails.DateOfFirstRegistration).getFullYear()
                                : 'N/A',  // Fixed this line
                            regNumber: vehicleDetails.Vrm,
                            engine: vehicleDetails.EngineCapacityCc || 'N/A',
                        };

                        try {
                            // Send vehicle data to the server for session storage
                            const response = await fetch('/store-vehicle-data', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                },
                                body: JSON.stringify(vehicleData),
                            });

                            const result = await response.json();
                            if (response.ok && result.success) {
                                // Redirect to the /service page
                                window.location.href = '/service';
                            } else {
                                alert(result.message || 'Failed to store vehicle data.');
                            }
                        } catch (error) {
                            console.error('Error storing vehicle data:', error.message);
                            alert('An error occurred while storing vehicle data.');
                        }
                    };

                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('svehicleDataModal'));
                    modal.show();
                } else {
                    modalContent.innerHTML = `<p>Error: ${result.error || 'Unable to load vehicle data.'}</p>`;
                }
            } catch (error) {
                console.error('Fetch Error:', error.message);
                modalContent.innerHTML = `<p class="text-red">Fetch Error: ${error.message}</p>`;
            }
        });
    }
});

function getCarServiceModel() {
    const make = document.getElementById('car_make').value;

    if (make) {
        // Use the selected make in the fetch URL
        fetch(`/models?make=${make}`)
            .then(response => {
                if (!response.ok) {
                    console.error('Error fetching models:', response.statusText);
                }
                return response.json();
            })
            .then(data => {
                // Clear the model dropdown and populate with fetched models
                const modelSelect = document.getElementById('car_model');
                modelSelect.innerHTML = '<option value="">-Model-</option>';
                data.forEach(item => {
                    modelSelect.innerHTML += `<option value="${item.Model}">${item.Model}</option>`;
                });
                modelSelect.disabled = false;
            })
            .catch(error => console.error('Fetch error:', error));
    }
}

function getCarServiceYear() {
    const make = document.getElementById('car_make').value;
    const model = document.getElementById('car_model').value;

    if (make && model) {
        fetch(`/years?make=${make}&model=${model}`)
            .then(response => response.json())
            .then(data => {
                const yearSelect = document.getElementById('car_year');
                yearSelect.innerHTML = '<option value="">-Year-</option>';

                // Loop through each entry in the data array
                data.forEach(yearRange => {
                    const years = yearRange.split('|'); // Split the years by "|"
                    years.forEach(year => {
                        // Add each year to the dropdown
                        yearSelect.innerHTML += `<option value="${year}">${year}</option>`;
                    });
                });

                yearSelect.disabled = false;
            })
            .catch(error => console.error('Error fetching years:', error));
    }
}

function getCarServiceEngine() {
    const make = document.getElementById('car_make').value;
    const model = document.getElementById('car_model').value;
    const year = document.getElementById('car_year').value;

    if (make && model && year) {
        fetch(`/engines?make=${make}&model=${model}&year=${year}`)
            .then(response => response.json())
            .then(engines => {
                const engineSelect = document.getElementById('car_engine');
                engineSelect.innerHTML = '<option value="">-Engine-</option>';
                engines.forEach(engine => {
                    engineSelect.innerHTML += `<option value="${engine}">${engine}</option>`;
                });
                engineSelect.disabled = false;
            });
    }
}

// Prevent the default form submission
document.addEventListener("DOMContentLoaded", function () {
    const carserviceSearchForm = document.getElementById('carserviceSearchForm');
    if (carserviceSearchForm) {
        carserviceSearchForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const make = document.getElementById('car_make').value;
            const model = document.getElementById('car_model').value;
            const year = document.getElementById('car_year').value;
            const engine = document.getElementById('car_engine').value ?? null;

            // Save selected values in sessionStorage
            const vehicleData = { make, model, year, engine };
            try {
                // Send vehicle data to the server for session storage
                const response = await fetch('/store-vehicle-data', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify(vehicleData),
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    // Redirect to the /service page
                    window.location.href = '/service';
                } else {
                    alert(result.message || 'Failed to store vehicle data.');
                }
            } catch (error) {
                console.error('Error storing vehicle data:', error.message);
                alert('An error occurred while storing vehicle data.');
            }

            // Redirect to the service page
            window.location.href = url;
        });
    }
});

// Function to update the cart count and total price
// Function to update the cart-top UI
function updateCartUI(data) {
    const subTotal = parseFloat(data.cartSubTotal);
    const vatTotal = parseFloat(data.vatTotal);
    const shippingPricePerJob = parseFloat(data.shippingPricePerJob);
    const shippingPricePerTyre = parseFloat(data.shippingPricePerTyre);
    const shippingVAT = parseFloat(data.shippingVAT);
    const grandTotal = parseFloat(data.cartTotalPrice);

    // Update totals in the UI
    $('#totalbill h4 #sub-total').text('£' + subTotal.toFixed(2));
    $('#totalbill h4 #vat-total').text('£' + vatTotal.toFixed(2));
    $('#totalbill h4 #grand-total').text('£' + grandTotal.toFixed(2));

    // Update callout charges if mobile fitting is present
    if (shippingPricePerJob > 0 || shippingPricePerTyre > 0) {
        $('.callout-charges').remove(); // Remove existing callout charges
        const calloutChargesHTML = `
            <h4 class="callout-charges">
                Callout Charges: £${(shippingPricePerJob + shippingPricePerTyre).toFixed(2)}
            </h4>
        `;
        $('.shopping-cart-total').append(calloutChargesHTML);
    } else {
        $('.callout-charges').remove(); // Remove callout charges if no mobile fitting
    }

    // Show or hide the "Your Basket is Empty" message and cart content
    if (data.remainingItems > 0) {
        $('.shopping-cart-content .text-center').hide();
        $('#cart-items-list, .shopping-cart-total, .shopping-cart-btn').show();
    } else {
        $('.shopping-cart-content .text-center').show();
        $('#cart-items-list, .shopping-cart-total, .shopping-cart-btn').hide();
    }
}


// Listen for changes in the filter inputs
document.querySelectorAll('.filter-input').forEach(function (input) {
    input.addEventListener('change', function () {
        // Collect the form data (input fields)
        const form = document.getElementById('tyreSearchForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);

        // Update the URL with the current form values without reloading the page
        const newUrl = window.location.pathname + '?' + params.toString();
        history.pushState({}, '', newUrl);

        // Optional: Trigger the AJAX request to fetch filtered tyres
        fetchFilteredTyres(params);
    });
});

// Function to perform the AJAX request and update the tyres list
function fetchFilteredTyres(params) {
    fetch('/tyres/search?' + params.toString())
        .then(response => response.text())
        .then(data => {
            document.getElementById('tyre-list').innerHTML = data;
        })
        .catch(error => console.error('Error fetching tyres:', error));
}

// On page load, pre-fill the form based on the URL query parameters
window.addEventListener('load', function () {
    const urlParams = new URLSearchParams(window.location.search);

    const widthInput = document.getElementById('width');
    const profileInput = document.getElementById('profile');
    const diameterInput = document.getElementById('diameter');

    if (widthInput) {
        widthInput.value = urlParams.get('width') || '';
    }

    if (profileInput) {
        profileInput.value = urlParams.get('profile') || '';
    }

    if (diameterInput) {
        diameterInput.value = urlParams.get('diameter') || '';
    }

    // Handle fitting_type radio buttons
    const fittingType = urlParams.get('fitting_type');
    if (fittingType) {
        const radioButton = document.querySelector(`input[name="fitting_type"][value="${fittingType}"]`);
        if (radioButton) {
            radioButton.checked = true;
        }
    }
});