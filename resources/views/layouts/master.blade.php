<!DOCTYPE html>
<html lang="zxx">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/x-icon" href="favicon.ico" />
        <title>Welcome Gamzoe</title>
        <meta name='description' content="" />
        <meta name="keywords" content="" />
        <meta name="it-rating" content="it-rat-cd303c3f80473535b3c667d0d67a7a11" />
        <meta name="cmsmagazine" content="3f86e43372e678604d35804a67860df7" />
        <link rel="stylesheet" type="text/css" href="{{ asset('asset/css/first-screen.css') }}" />
        <!-- <link rel="stylesheet" type="text/css" href="css/first-screen-inner.css" /> -->
        <link rel="preload " href="{{ asset('asset/fonts/AleoBold.woff2') }}" as="font" crossorigin>
        <link rel="preload " href="{{ asset('asset/fonts/Lato/LatoRegular.woff2') }}" as="font" crossorigin>
        <link rel="preload " href="{{ asset('asset/fonts/Lato/LatoBold.woff2') }}" as="font" crossorigin>
        <link rel="preload"  href="{{ asset('asset/css/style.css') }}" as="style">
    </head>
    <body class="home loaded">
        <div class="main-wrapper">
            <main class="content">
                <div class="first-screen section-screen-main">
                    <div class="section-screen-main__bg" style="background-image: url({{ asset('asset/img/main.svg') }});"></div>
                    <div class="wrapper">
                        <div class="screen-main">
                            <div class="section-heading"><span>Be sure</span></div>
                            <h1 class="h1 h1-main">play and win in&nbsp;your&nbsp; prizes</h1>
                            <div class="screen-main__text">Agency with 12&nbsp;years of history, 15&nbsp;employees, Fortune 5000&nbsp;clients and proven results.</div>
                            @if(Auth::user())
                            <a href="#game" class="btn btn_learn">Play Game</a>
                            @else
                            <a href="{{ url('auth/google') }}" class="btn btn_learn">Get Started</a>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="section-about" id="about">
                    <div class="wrapper">
                        <div class="about">
                            <div class="about__img">
                                <div class="about__picture">
                                    <img data-src="{{ asset('asset/img/way.svg') }}" alt="" class="js-lazy" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=">
                                </div>
                            </div>
                            <div class="about__content">
                                <div class="section-heading"><span>the history</span></div>
                                <div class="h2">Our way to succesful future</div>
                                <div class="section-subtitle">Sint nulla commodo qui magna eiusmod quis aliqua laboris officia excepteur non eu in.</div>
                                <div class="content-block__text">
                                    <p>Dolor duis voluptate enim exercitation consequat ex. Voluptate in sunt commodo aute do. Dolor enim dolor labore velit nulla sit exercitation irure esse proident velit commodo. Est non officia proident esse culpa commodo nulla Lorem do enderit esse do.</p>
                                </div>
                                <a href="https://www.youtube.com/watch?v=_sI_Ps7JSEk" class="about__btn play-video js-fancybox">
                                    <span class="play-icon">
                                    <i class="icon-play"></i>
                                    </span>
                                    <div class="play-video__text">
                                        <div class="play-video__title">about us</div>
                                        <div class="play-video__link">Watch our process!</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="about-details">
                            <div class="about-details__item">
                                <div class="about-details__val">$ 35k<span class="about-details__val_plus">+</span></div>
                                <div class="about-details__text">Clients revenue</div>
                                <div class="about-details__decor"></div>
                            </div>
                            <div class="about-details__item">
                                <div class="about-details__val">16k<span class="about-details__val_plus">+</span></div>
                                <div class="about-details__text">Leads for clients</div>
                                <div class="about-details__decor"></div>
                            </div>
                            <div class="about-details__item">
                                <div class="about-details__val">6.7k<span class="about-details__val_plus">+</span></div>
                                <div class="about-details__text">Phone calls</div>
                                <div class="about-details__decor"></div>
                            </div>
                            <div class="about-details__item">
                                <div class="about-details__val">254<span class="about-details__val_plus">+</span></div>
                                <div class="about-details__text">Successful projects</div>
                                <div class="about-details__decor"></div>
                            </div>
                        </div>
                    </div>
                    <div class="about-decor about-decor_1"></div>
                    <div class="about-decor about-decor_2"></div>
                    <div class="about-decor about-decor_3"></div>
                </div>
                <div class="section-get" id="winnings">
                    <div class="wrapper">
                        <div class="section-heading h-center"><span>Potential Winnings</span></div>
                        <div class="h-decor-1">
                            <h2 class="h2 h-center"><span>what you will get with us</span></h2>
                            <div class="section-subtitle h-center">Play some trivia games and win prizes </div>
                        </div>
                        <div class="get-list">
                            <div class="get-list__item">
                                <div class="get-list__heading">
                                    <div class="get-list__icon">
                                        <img src="{{ asset('asset/img/icons-svg/get-1.svg') }}"  alt="" loading="lazy">
                                    </div>
                                    <div class="get-list__title">Cash</div>
                                </div>
                                <div class="get-list__text">Dolor duis voluptate enim exercitation consequat ex. Voluptate in sunt commodo aute do. Dolor enim dolor labore velit nulla sit exercitation irure esse proid.</div>
                            </div>
                            <div class="get-list__item">
                                <div class="get-list__heading">
                                    <div class="get-list__icon">
                                        <img src="{{ asset('asset/img/icons-svg/get-2.svg') }}"  alt="" loading="lazy">
                                    </div>
                                    <div class="get-list__title">Recharge Cards</div>
                                </div>
                                <div class="get-list__text">Voluptate in sim dolor labore velit nuunt commodo aute do. Dolor enim dolor labore im dolor labore velit te dolor enim dolor labore velit nul.</div>
                            </div>
                            <div class="get-list__item">
                                <div class="get-list__heading">
                                    <div class="get-list__icon">
                                        <img src="{{ asset('asset/img/icons-svg/get-3.svg') }}"  alt="" loading="lazy">
                                    </div>
                                    <div class="get-list__title">Data Bundles</div>
                                </div>
                                <div class="get-list__text">Pariatur magna cupidatat magna sit incididunt non pariatur. Sint nulla commodo qui magna eiusmod quis aliqua laboris officia excepteur non eu in.</div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="section-consultation" id="contact">
                    <div class="section-consultation__bg js-lazy" data-src="{{ asset('asset/img/bg/bg-2.svg') }}"></div>
                    <div class="wrapper">
                        <div class="consultation-form-wrap">
                            <div class="consultation-form">
                                <div class="section-heading"><span>get started</span></div>
                                <h2 class="h2">get in touch with us</h2>
                                <div class="content-block__text">
                                    <p>You have any concern or suggesion, reach out to us here</p>
                                </div>
                                <div class="consultation-form__form">
                                    <form onsubmit="successSubmit();return false;">
                                        <div class="box-fileds">
                                            <div class="box-filed">
                                                <input type="text" placeholder="First name">
                                            </div>
                                            
                                            <div class="box-filed">
                                                <input type="email" placeholder="Enter your email">
                                            </div>
                                            <div class="box-filed">
                                                <textarea name="message" placeholder="We would love to have a feel"> </textarea>
                                                {{--  <input type="text" placeholder="Second name">  --}}
                                            </div>
                                            <div class="box-filed">
                                                {{--  <input type="text" placeholder="Second name">  --}}
                                            </div>
                                            <div class="box-filed box-filed_btn">
                                                <input type="submit" class="btn" value="Submit">
                                            </div>
                                        
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="consultation-img">
                                <img data-src="{{ asset('asset/img/consultation.svg') }}" alt="" class="js-lazy" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=">
                            </div>
                        </div>
                    </div>
                </div>
                
                {{--    --}}
                
                <div class="section-newsletter">
                    <div class="section-newsletter__bg js-lazy" data-src="{{ asset('asset/img/bg/bg-3.svg') }}"></div>
                    <div class="wrapper">
                        <div class="newsletter">
                            <div class="newsletter__content">
                                <h3 class="h3">Newsletter</h3>
                                <div class="newsletter__text">
                                    <p>Pariatur magna cupidatat magna sit incididunt non pariatur. Sint nulla commodo qui magna eiusmod quis aliqua laboris officia excepteur non eu in.</p>
                                </div>
                                <form>
                                    <div class="box-fileds-newsletter">
                                        <div class="box-filed box-filed_1">
                                            <input type="email" placeholder="Enter your email">
                                        </div>
                                        <div class="box-filed box-filed_submit">
                                            <input type="submit" class="btn" value="Subscribe">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="newsletter__img">
                                <div class="newsletter__picture">
                                    <img data-src="{{ asset('asset/img/newsletter.svg') }}" alt="" class="js-lazy" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section-testimonials" id="testimonials">
                    <div class="wrapper">
                        <div class="testimonials">
                            <div class="testimonials__img">
                                <div class="testimonials__picture">
                                    <img data-src="{{ asset('asset/img/testimonials.svg') }}" alt="" class="js-lazy" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=">
                                </div>
                            </div>
                            <div class="testimonials__content">
                                <div class="section-heading"><span>they say</span></div>
                                <div class="h2">Testimonials</div>
                                <div class="swiper-container reviews-slider js-slider-1">
                                    <div class="swiper-wrapper">
                                        <div class="swiper-slide testimonials-card">
                                            <div class="testimonials-card__text">
                                                <p>“Dolor duis voluptate enim exercitation consequat ex. Voluptate in sunt commodo aute do. Dolor enim dolor labore velit nulla sit exercitation irure esse proident.”</p>
                                            </div>
                                            <div class="author">
                                                <img class="author__img js-lazy" data-src="{{ asset('asset/img/examples/avatar_1.jpg') }}" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" alt="">
                                                <div class="author__details">
                                                    <div class="author__title">Kathryn Murphy</div>
                                                    <div class="author__position">Marketing Coordinator</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="swiper-slide testimonials-card">
                                            <div class="testimonials-card__text">
                                                <p>“2 Dolor duis voluptate enim exercitation consequat ex. Voluptate in sunt commodo aute do. Dolor enim dolor labore velit nulla sit exercitation”</p>
                                            </div>
                                            <div class="author">
                                                <img class="author__img js-lazy" data-src="{{ asset('asset/img/examples/avatar_1.jpg') }}" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" alt="">
                                                <div class="author__details">
                                                    <div class="author__title">Kathryn Murp</div>
                                                    <div class="author__position">Marketing Coord</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </main>
            <header class="header" id="header">
                <div class="header-top">
                    <div class="wrapper">
                        <div class="socials ">
                            <div class="footer-title">Find us here:</div>
                            <div class="socials">
                                <div class="socials__item">
                                    <a href="#" target="_blank" class="socials__link">Facebook</a>
                                </div>
                                <div class="socials__item">
                                    <a href="#" target="_blank" class="socials__link">Instagram</a>
                                </div>
                            </div>
                        </div>
                        {{--  <div class="phone-item">
                            <div class="footer-title header-title-phone">Have a question? Call us!</div>
                            <div class="footer-phone__item">
                                <i class="icon-phone"></i><a href="tel:+15469872185">+1 546 987 21 85</a>
                            </div>
                        </div>  --}}
                    </div>
                </div>
                <div class="wrapper">
                    <div class="nav-logo">
                        <a href="index.html" class="logo">
                            <img src="{{ asset('asset/img/logo.svg') }}" alt="Numerio">
                        </a>
                    </div>
                    <div class="header-right">
                        <div id="mainNav" class="menu-box">
                            @if(Auth::user())
                            <nav class="nav-inner">
                                <ul class="main-menu js-menu" id="mainMenu">
                                    <li>
                                        <a href="#about">About Us</a>
                                    </li>
                                    <li>
                                        <a href="#contact">Contact Us</a>
                                    </li>
                                    <li>
                                        <a href="#winnings">What You Get</a>
                                    </li>
                                    
                                    <li>
                                        <a href="#testimonials">Testimonials</a>
                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>
                                    </li>
                                    
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                            
                                </ul>
                            </nav>
                            @else
                            <nav class="nav-inner">
                                <ul class="main-menu js-menu" id="mainMenu">
                                    <li>
                                        <a href="#about">About Us</a>
                                    </li>
                                    <li>
                                        <a href="#contact">Contact Us</a>
                                    </li>
                                    <li>
                                        <a href="#winnings">What You Get</a>
                                    </li>
                                    
                                    <li>
                                        <a href="#testimonials">Testimonials</a>
                                    </li>
                                </ul>
                            </nav>
                            @endif
                            <div class="socials-item">
                                <div class="footer-title">Find us here:</div>
                                <div class="socials">
                                    <div class="socials__item">
                                        <a href="#" target="_blank" class="socials__link">Fb</a>
                                    </div>
                                    <div class="socials__item">
                                        <a href="#" target="_blank" class="socials__link">Ins</a>
                                    </div>
                                </div>
                            </div>
                            {{--  <div class="phone-item">
                                <div class="footer-title footer-title_phone">Have a question? Call us!</div>
                                <div class="footer-phone__item">
                                    <i class="icon-phone"></i><a href="tel:+15469872185">+1 546 987 21 85</a>
                                </div>
                            </div>  --}}
                        </div>
                        @if(Auth::user())
                        <a href="{{ route('take.quiz') }}" class="btn-2 btn_started-header">Play Games</a>
                        @else
                        <a href="{{ url('auth/google') }}" class="btn-2 btn_started-header">get started</a>
                        @endif
                    </div>
                    <div class="bars-mob js-button-nav">
                        <div class="hamburger">
                            <span></span><span></span><span></span>
                        </div>
                        <div class="cross">
                            <span></span><span></span>
                        </div>
                    </div>
                </div>
            </header>
            <footer id="footer" class="footer footer-2">
                <div class="footer__bg js-lazy" data-src="img/bg/footer-2.svg"></div>
                <div class="wrapper">
                    <a href="{{ url('/') }}" class="logo-footer">
                        <img src="{{ asset('asset/img/logo.svg') }}" alt="Gamzoe">
                    </a>
                    <div class="socials-item footer-social">
                        <div class="footer-title">Find us here:</div>
                        <div class="socials">
                            <div class="socials__item">
                                <a href="#" target="_blank" class="socials__link">Fb</a>
                            </div>
                            <div class="socials__item">
                                <a href="#" target="_blank" class="socials__link">Ins</a>
                            </div>
                            
                        </div>
                    </div>
                    {{--  <div class="phone-item footer-phone">
                        <div class="footer-title footer-title_phone">Have a question? Call us!</div>
                        <div class="footer-phone__item">
                            <i class="icon-phone"></i><a href="tel:+15469872185">+1 546 987 21 85</a>
                        </div>
                    </div>  --}}
                    <a href="{{ url('auth/google') }}" class="btn-2 btn_started">get started</a>
                    {{--  <a href="#formOrder" class="btn-2 btn_started js-fancybox">get started</a>  --}}
                </div>
                <div class="footer-bottom">
                    <div class="wrapper">
                        <div class="copyrights">©All rights reserved. Gamzoe  {{ date('Y') }}</div>
                        <div class="footer-menu">
                            <ul class="js-menu-footer">
                                <li>
                                    <a href="#services">Services</a>
                                </li>
                                <li>
                                    <a href="#about">About</a>
                                </li>
                                <li>
                                    <a href="#steps">Steps</a>
                                </li>
                                <li>
                                    <a href="#price">Price</a>
                                </li>
                                <li>
                                    <a href="#testimonials">Testimonials</a>
                                </li>
                                <li>
                                    <a href="#blog">Blog</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- BODY EOF   -->
        <!-- popups -->
        <div class="window-open">
            <div class="popup" id="formOrder" tabindex="0">
                <div class="block-popup">
                    <div class="popup-title-wrap">
                        <div class="popup-title">Get a free consultation</div><div class="popup-decor-top"></div>
                    </div>
                    <div class="popup-text">Culpa non ex tempor qui nulla laborum. Laboris culpa ea incididunt dolore ipsum tempor duis do ullamc.</div>
                    
                    {{--  <form onsubmit="successSubmit();return false;">
                        <div class="popup-form">
                            <div class="box-field">
                                <input type="text" placeholder="Name">
                            </div>
                            <div class="box-field">
                                <input type="email"  placeholder="Email">
                            </div>
                            <div class="box-field">
                                <textarea placeholder="Message"></textarea>
                            </div>
                            <div class="box-fileds box-fileds_2">
                                <div class="box-filed box-filed_btn">
                                    <input type="submit" class="btn" value="Submit">
                                </div>
                                <div class="box-filed box-field__accept">
                                    <label class="checkbox-element">
                                    <input type="checkbox" >
                                    <span class="checkbox-text">I accept the <a href="#" target="_blank">Terms and Conditions.</a></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>  --}}
                </div>
                <div class="popup-decor"></div>
            </div>
            <div class="popup popup-succsess" id="succsesOrder">
                <div class="block-popup">
                    <div class="popup-title"><span>Sank you</span></div>
                    <div class="popup-result">Dolor duis voluptate enim exercitation consequat ex. Voluptate </div>
                    <svg width="200" height="184" viewBox="0 0 200 184" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M178.566 91.6643C178.566 139.987 139.392 179.162 91.0686 179.162C42.744 179.162 3.57129 139.987 3.57129 91.6643C3.57129 43.3397 42.744 4.16699 91.0686 4.16699C139.392 4.16699 178.566 43.3397 178.566 91.6643Z" fill="rgba(255, 154, 160, 0.991703)"/>
                        <path d="M91.6644 183.327C41.1242 183.327 0 142.205 0 91.6644C0 41.1242 41.1242 0 91.6644 0C109.23 0 126.33 5.01694 141.112 14.4908C144.02 16.3585 144.863 20.2249 143.004 23.124C141.138 26.0246 137.262 26.8745 134.371 25.0084C121.597 16.833 106.839 12.4996 91.6644 12.4996C48.0149 12.4996 12.4996 48.0149 12.4996 91.6644C12.4996 135.312 48.0149 170.828 91.6644 170.828C135.312 170.828 170.828 135.312 170.828 91.6644C170.828 89.0552 170.703 86.472 170.461 83.9315C170.129 80.4984 172.645 77.4391 176.086 77.1141C179.494 76.6731 182.569 79.2975 182.903 82.7307C183.185 85.6725 183.327 88.6478 183.327 91.6644C183.327 142.205 142.205 183.327 91.6644 183.327Z" fill="rgba(249, 73, 115, 0.991703)"/>
                        <path d="M102.08 112.496C100.481 112.496 98.8799 111.887 97.6638 110.663L60.165 73.1643C57.7237 70.7214 57.7237 66.7634 60.165 64.3221C62.6063 61.8808 66.5643 61.8808 69.0057 64.3221L102.089 97.4052L189.327 10.1657C191.77 7.72438 195.728 7.72438 198.169 10.1657C200.61 12.607 200.61 16.5651 198.169 19.0064L106.505 110.671C105.279 111.887 103.68 112.496 102.08 112.496Z" fill="rgba(249, 73, 115, 0.991703)"/>
                    </svg>
                    <div class="popup-text">Dolor duis voluptate enim exercitation consequat ex. Voluptate </div>
                    <div class="popup-button_succsees">
                        <div class="btn btn-popup" data-fancybox-close>Ok</div>
                    </div>
                </div>
            </div>
        </div>        
        <script>
            var body = document.body;
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
                body.classList.add('ios');
            } else {
                body.classList.add('web')
            }
            setTimeout(function() {
                body.classList.add("content-loaded");
            },50)
                
        </script>
        <link rel="stylesheet" type="text/css" href="{{ asset('asset/css/style.css') }}" />
        <script src="{{ asset('asset/js/jquery-3.5.1.min.js') }}" defer></script>
        <script src="{{ asset('asset/js/components/jquery.lazy.min.js') }}" defer></script>
        <script src="{{ asset('asset/js/components/jquery.fancybox.min.js') }}" defer></script>
        <script src="{{ asset('asset/js/components/jquery.singlePageNav.min.js') }}" defer></script>
        <script src="{{ asset('asset/js/components/swiper.js') }}" defer></script>
        <script src="{{ asset('asset/js/custom.js') }}" defer></script>
    </body>
</html>