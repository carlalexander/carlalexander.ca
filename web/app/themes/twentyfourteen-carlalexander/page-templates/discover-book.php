<?php
/**
 * Template Name: Discover book
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) & !(IE 8)]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">

    <link href="<?= get_stylesheet_directory_uri(); ?>/css/material-kit.min.css" rel="stylesheet" />
    <link href="<?= get_stylesheet_directory_uri(); ?>/css/discover.css" rel="stylesheet" />
    <?php twentyfourteen_output_seo(); ?>
    <?php twentyfourteen_output_analytics(); ?>
</head>

<body <?php body_class(); ?>>

<div class="page-header header-filter" data-parallax="true" style="background-image: url('<?= get_stylesheet_directory_uri(); ?>/images/discover-top-desk.jpg');">
    <div class="container">
        <div class="row">
            <div class="offset-md-6 col-md-6">
                <h1 class="title">Wish you could learn object-oriented programming?</h1>
                <p class="h4"><em>"Discover object-oriented programming using WordPress"</em> is a book and video course <u><span class="font-weight-bold">designed for WordPress developers.</span></u> It'll teach you the fundamentals of object-oriented programming using WordPress concepts and terminology. This way you can get the hang of this important topic so that you can <u><span class="font-weight-bold">start using it in your WordPress projects.</span></u></p>
                <p>
                    <a href="#packages" class="btn btn-primary btn-raised btn-round btn-lg font-weight-bold">View packages</a> <a href="#sample" class="bg-light btn btn-outline-primary btn-round btn-lg font-weight-bold border-0">Get a sample</a>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="main main-raised">
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-7 ml-auto mr-auto">
                    <h3 class="title text-center">“I know I should learn this. I just don't have the time.”</h3>
                    <p class="h5">It’s no secret that programming requires a lot of work. You have plenty of things to manage, too. Your work has deadlines, bosses, clients, prospects, you name it. As a WordPress developer (or any developer, really), it’s hard to catch a break.</p>
                    <p class="h5">Meanwhile, the tools and technologies you use are always changing. The tech world isn’t going to stop. There’s React, Sass, Vagrant, APIs, just to name a few. And that doesn't even touch the server-side of our work. It’s a full time job just staying ahead.</p>
                    <p class="h5">This leaves you with little time to learn advanced programming concepts like object-oriented programming. It doesn’t help that that stuff is hard even if there’s plenty of tutorials out there.</p>
                    <h3 class="title">Most of those tutorials suck too</h3>
                    <p class="h5">You’ve probably read some of them already. They talk about dogs and cars. When’s the last time you coded a car? That's not how the real world works. Where are those practical examples that you can use at work or on your personal project?</p>
                    <p class="h5">So you end up with the same result. The whole thing makes no sense to you! You end up telling yourself it’s not useful or worth the trouble.</p>
                    <h3 class="title">There’s plenty of reasons to learn object-oriented programming</h3>
                    <p class="h5">Whether it’s to make more money, save time or just to build expertise so you can move on to something else in your career, object-oriented programming is worth your time.</p>
                    <p class="h5"><strong>It’s just that object-oriented programming isn’t easy.</strong></p>
                    <p class="h5">But, if you're ready to really get started with object-oriented programming, look no further. You've found the right resource to help you with that.</p>
                </div>
            </div>
            <div class="features text-center">
                <div class="row">
                    <div class="col-md-7 ml-auto mr-auto">
                        <h2 class="title">What do you get?</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info">
                            <div class="icon" style="height: 68px">
                                <i class="fa fa-book"></i>
                            </div>
                            <h4 class="info-title">164-page book</h4>
                            <p>Everything that you need to familiarize yourself with object-oriented programming explained with WordPress terminology.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info">
                            <div class="icon">
                                <i class="material-icons">code</i>
                            </div>
                            <h4 class="info-title">Exercises</h4>
                            <p>A comprehensive set of 18 exercises as well as their solutions so that you can practice the fundamentals of object-oriented programming.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info">
                            <div class="icon">
                                <i class="material-icons">ondemand_video</i>
                            </div>
                            <h4 class="info-title">In-depth screencasts</h4>
                            <p>Every solution to the exercises from the book explained in detail giving you all the context that you need to understand how object-oriented programming works.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="subscribe-line subscribe-line-image" style="background-image: url('<?= get_stylesheet_directory_uri(); ?>/images/escheresque_ste.png');">
            <div class="container">
                <div id="sample" class="row">
                    <div class="col-md-7 ml-auto mr-auto">
                        <div class="text-center">
                            <h3 class="title text-white">Get a free sample</h3>
                            <p class="description text-white">
                                Get a sneak peek at the book. Enter your email address below, and you'll get a sample chapter of the book as well as the table of contents.
                            </p>
                        </div>
                        <div class="card card-raised card-form-horizontal">
                            <div class="card-body">
                                <form id="ck_subscribe_form" action="https://app.convertkit.com/landing_pages/167881/subscribe" data-remote="true">
                                    <input type="hidden" name="id" value="167881" id="landing_page_id">
                                    <input type="hidden" name="ck_form_recaptcha" value="" id="ck_form_recaptcha">

                                    <div class="row">
                                        <div class="col-md-4">
                                            <span class="form-group bmd-form-group">
                                                <input type="text" name="first_name" id="ck_firstNameField" placeholder="First name (optional)" class="form-control">
                                            </span>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="form-group bmd-form-group">
                                                <input type="email" name="email" id="ck_emailField" placeholder="Email address" class="form-control" required>
                                            </span>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <button class="subscribe_button ck_subscribe_button btn btn-primary btn-round" id="ck_subscribe_button">
                                                Get it now
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="row">
                                    <div id="ck_error_msg" class="col-md-12 text-center" style="display: none;">
                                        <span class="font-weight-bold text-danger">Something went wrong, please try again.</span>
                                    </div>
                                    <div id="ck_success_msg" class="col-md-12 text-center" style="display: none;">
                                        <span class="font-weight-bold text-success">Yay! Check your email for a download link.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-7 ml-auto mr-auto">
                    <h2 class="title">Testimonials</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 ml-auto mr-auto">
                    <div class="card card-profile card-plain card-testimonial">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card-header card-header-image">
                                    <img class="img" src="<?= get_stylesheet_directory_uri(); ?>/images/rachel-cherry.jpg">
                                    <div class="colored-shadow" style="background-image: url('<?= get_stylesheet_directory_uri(); ?>/images/rachel-cherry.jpg'); opacity: 1;"></div></div>
                            </div>
                            <div class="col-md-9">
                                <div class="card-body">
                                    <p>
                                        Carl has a zest for teaching that is a joy to experience. With his depth of knowledge and experience, he breaks down complex topics in a manner that’s both encouraging and inviting. If you want to increase the quality of your WordPress code, this book is a must-read.
                                    </p>
                                    <div class="h4 card-title">Rachel Cherry</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 ml-auto mr-auto">
                    <div class="card card-profile card-plain card-testimonial">
                        <div class="row">
                            <div class="col-md-3 hidden-md-up">
                                <div class="card-header card-header-image">
                                    <img class="img" src="<?= get_stylesheet_directory_uri(); ?>/images/zac-gordon.jpg">
                                    <div class="colored-shadow" style="background-image: url('<?= get_stylesheet_directory_uri(); ?>/images/zac-gordon.jpg'); opacity: 1;"></div></div>
                            </div>
                            <div class="col-md-9">
                                <div class="card-body">
                                    <p>
                                        I love learning from Carl. He has a command of some very advanced topics and always gets into how things work and why certain approaches might be better than others. Top notch!
                                    </p>
                                    <div class="h4 card-title">Zac Gordon</div>
                                </div>
                            </div>
                            <div class="col-md-3 hidden-sm-down">
                                <div class="card-header card-header-image">
                                    <img class="img" src="<?= get_stylesheet_directory_uri(); ?>/images/zac-gordon.jpg">
                                    <div class="colored-shadow" style="background-image: url('<?= get_stylesheet_directory_uri(); ?>/images/zac-gordon.jpg'); opacity: 1;"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 ml-auto mr-auto">
                    <div class="card card-profile card-plain card-testimonial">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card-header card-header-image">
                                    <img class="img" src="<?= get_stylesheet_directory_uri(); ?>/images/tessa-kriesel.jpg">
                                    <div class="colored-shadow" style="background-image: url('<?= get_stylesheet_directory_uri(); ?>/images/tessa-kriesel.jpg'); opacity: 1;"></div></div>
                            </div>
                            <div class="col-md-9">
                                <div class="card-body">
                                    <p>
                                        Carl is a fantastic instructor. He breaks things down in a way that no matter what your skill set is, you can understand. When presenting in person, he breaks often and allows for questions to ensure that people are able to follow along and not get lost in the deep content he is sharing. He is able to explain very complex topics easily.
                                    </p>
                                    <div class="h4 card-title">Tessa Kriesel</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7 ml-auto mr-auto">
                    <div class="card card-profile card-plain card-testimonial">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="col-md-3 hidden-md-up">
                                    <div class="card-header card-header-image">
                                        <img class="img" src="<?= get_stylesheet_directory_uri(); ?>/images/josh-pollock.jpg">
                                        <div class="colored-shadow" style="background-image: url('<?= get_stylesheet_directory_uri(); ?>/images/josh-pollock.jpg'); opacity: 1;"></div></div>
                                </div>
                                <div class="card-body">
                                    <p>
                                        I wish that this book had existed when I first got serious about learning PHP for WordPress development. What I did have was Carl's blog, his post about of polymorphism was my introduction to what PHP is capable of and the importance of understanding language and programming fundamentals.
                                    </p>
                                    <div class="h4 card-title">Josh Pollock</div>
                                </div>
                            </div>
                            <div class="col-md-3 hidden-sm-down">
                                <div class="card-header card-header-image">
                                    <img class="img" src="<?= get_stylesheet_directory_uri(); ?>/images/josh-pollock.jpg">
                                    <div class="colored-shadow" style="background-image: url('<?= get_stylesheet_directory_uri(); ?>/images/josh-pollock.jpg'); opacity: 1;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="packages" class="row">
                <div class="col-md-7 ml-auto mr-auto">
                    <h2 class="title">Packages</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-pricing card-plain">
                        <div class="card-body">
                            <div class="h6 card-category text-info">The book</div>
                            <div class="h1 card-title"><small class="currency">$</small>39</div>
                            <ul class="list-unstyled">
                                <li><strong>The 164-page</strong> book in PDF format</li>
                                <li><strong>Lifetime access</strong> to book updates</li>
                            </ul>
                            <a href="" class="btn btn-primary btn-round">Buy now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-pricing card-raised bg-primary">
                        <div class="card-body">
                            <div class="h6 card-category text-white">Premium package</div>
                            <div class="h1 card-title"><small class="currency">$</small>99</div>
                            <ul class="list-unstyled">
                                <li>Set of <strong>18 exercises</strong> + <strong>solutions</strong></li>
                                <li><strong>The 164-page</strong> book in PDF format</li>
                                <li><strong>Lifetime access</strong> to book updates</li>
                            </ul>
                            <a href="#pablo" class="btn bg-light btn-outline-primary btn-round">Buy now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-pricing card-plain">
                        <div class="card-body">
                            <div class="h6 card-category text-info">Complete package</div>
                            <div class="h1 card-title"><small class="currency">$</small>249</div>
                            <ul class="list-unstyled">
                                <li><strong>In-depth screencasts</strong> covering all the exercises</li>
                                <li>Set of <strong>18 exercises</strong> + <strong>solutions</strong></li>
                                <li><strong>The 164-page</strong> book in PDF format</li>
                                <li><strong>Lifetime access</strong> to book updates</li>
                            </ul>
                            <a href="#pablo" class="btn btn-primary btn-round">Buy now</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="offset-md-2 col-md-6 text-left">
                    <h3>Looking to get this for your team?</h3>
                    <p>You can get <strong>unlimited licenses of the complete package</strong> as well as a <strong>two code review sessions</strong> (a $400 value) for <strong>$999</strong>. A great deal if you're looking to improve your team's development practices!</p>
                </div>
                <div class="col-md-3 text-center d-flex">
                    <button type="button" class="btn btn-primary btn-round align-self-center mx-auto">Buy for your team</button>
                </div>
            </div>
        </div>
    </div>
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-7 ml-auto mr-auto">
                    <div class="text-center">
                        <img src="<?= get_stylesheet_directory_uri(); ?>/images/carl-alexander.jpg" width="150" height="150" alt="Carl Alexander" class="img-raised rounded-circle img-fluid">
                        <h2 class="title">About the author</h2>
                    </div>
                    <p>Heya, I’m Carl Alexander!</p>
                    <p>I'm the charming guy that you can see above. I've been teaching everything I know about programming to the WordPress community for a few years now. Most of it you can find on <a href="https://carlalexander.ca">my site</a>, but I've also done a fair share of <a href="https://wordpress.tv/speakers/carl-alexander/">speaking at WordCamps</a> as well.</p>
                </div>
            </div>
            <div class="text-center">
                <h2 class="title">Frequently Asked Questions</h2>
            </div>
            <div class="row">
                <div class="col-md-5 ml-auto">
                    <h4 class="info-title">Is there a physical copy of the book?</h4>
                    <p>I might look into making a physical version of the book in the future. But for now, you can only get the book as a PDF.</p>
                </div>
                <div class="col-md-5 mr-auto">
                    <h4 class="info-title">Can I upgrade to another package later?</h4>
                    <p>Yup, that's not a problem at all! You can send me an email at <a href="mailto:carl@carlalexander.ca">carl@carlalexander.ca</a>, and I'll help you out with that.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 ml-auto">
                    <h4 class="info-title">What if I end up not liking it?</h4>
                    <p>Then I don't want your money. Seriously! Just email me at <a href="mailto:carl@carlalexander.ca">carl@carlalexander.ca</a>, and I'll refund your purchase. No questions asked.</p>
                </div>
                <div class="col-md-5 mr-auto">
                    <h4 class="info-title">I still have a question!</h4>
                    <p>I'm happy to answer any other questions you might have! You can reach me at <a href="mailto:carl@carlalexander.ca">carl@carlalexander.ca</a>, and I'll answer you as soon as possible.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="footer footer-default">
    <div class="container copyright">&copy; <?= date('Y'); ?> Carl Alexander. All Rights Reserved.</div>
</footer>
<script src="https://cdn.convertkit.com/assets/CKJS4.js?v=21"></script>
</body>
</html>
