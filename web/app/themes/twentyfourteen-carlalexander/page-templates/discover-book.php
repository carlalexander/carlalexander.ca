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
                    <h3 class="title">[Need a title]</h3>
                    <p class="h5">It’s no secret that programming requires a lot of work. You have plenty of things to stay on top of. Your work has deadlines, bosses, clients, prospects, you name it. As WordPress developer (or any developer really), it’s hard to catch a break.</p>
                    <p class="h5">Meanwhile, the tools and technologies you use change all the time. The tech world isn’t going to stop for you after all. There’s Backbone, Sass, Vagrant, APIs, just to name a few. That’s without touching the server stuff. It’s a full time job just staying ahead.</p>
                    <p class="h5">This leaves you with little time to learn advanced programming concepts like object-oriented programming. It doesn’t help that that stuff is hard. Even if there’s plenty of tutorials out there.</p>
                    <h3 class="title">Most of those tutorials suck too</h3>
                    <p class="h5">You’ve probably read some of them already. They talk of dogs and cars. When’s the last time you coded a car? That’s just not how real life works. Where are those practical examples that you can use at work or on your personal project?</p>
                    <p class="h5">So you end up with the same result. The whole thing makes no sense to you! You end up telling yourself it’s not useful or worth the trouble.</p>
                    <h3 class="title">There’s plenty of reasons to learn object-oriented programming</h3>
                    <p class="h5">Whether it’s to make more money, save time or just to build expertise so you can move on to something else in your career. Object-oriented programming is worth your time.</p>
                    <p class="h5"><strong>It’s just that object-oriented programming isn’t easy.</strong></p>
                    <p class="h5">But, if you're ready to get started with object-oriented programming, look no further. You've found the right resource to help you with that.</p>
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
                            <h4 class="info-title">[148-page book]</h4>
                            <p>[No introduction, personal anecdotes or stories about my childhood. Just real-world, practical refactoring content, right from page one.]</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info">
                            <div class="icon">
                                <i class="material-icons">code</i>
                            </div>
                            <h4 class="info-title">Exercises</h4>
                            <p>[A comprehensive set of 25 exercises in the form of unit tests, to practice your chops and make sure you have these patterns under your fingers.]</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info">
                            <div class="icon">
                                <i class="material-icons">ondemand_video</i>
                            </div>
                            <h4 class="info-title">[4 hours of video]</h4>
                            <p>[Every refactoring example from the book covered in even more depth, as well as three additional advanced tutorials, making up almost 2 hours of content on their own.]</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="section">
        <div class="subscribe-line subscribe-line-image" style="background-image: url('<?= get_stylesheet_directory_uri(); ?>/images/escheresque_ste.png');">
            <div class="container">
                <div id="sample" class="row">
                    <div class="col-md-7 ml-auto mr-auto">
                        <div class="text-center">
                            <h3 class="title text-white">Get a free sample</h3>
                            <p class="description text-white">
                                [You'll also join my book launch list. This will give you access to free lessons and an early discount when I release the book.]
                            </p>
                        </div>
                        <div class="card card-raised card-form-horizontal">
                            <div class="card-body">
                                <form id="ck_subscribe_form" action="https://forms.convertkit.com/landing_pages/167881/subscribe" data-remote="true">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <span class="form-group bmd-form-group">
                                                <input type="text" name="first_name" id="ck_firstNameField" placeholder="First name (optional)" class="form-control">
                                            </span>
                                        </div>
                                        <div class="col-md-4">
                                            <span class="form-group bmd-form-group">
                                                <input type="email" name="email" id="ck_emailField" placeholder="Email address" class="form-control" required="">
                                            </span>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <button type="button" class="btn btn-primary btn-round">Get it now</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="container text-center">
            <div class="row">
                <div class="col-md-7 ml-auto mr-auto">
                    <h2 class="title">Testimonials</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="team-player">
                        <div class="card card-plain">
                            <div class="col-md-6 ml-auto mr-auto">
                                <img src="https://carlalexander.ca/app/uploads/2015/10/carl-120px.jpg" class="img-raised rounded-circle img-fluid">
                            </div>
                            <h4 class="card-title">Rachel Cherry</h4>
                            <div class="card-body">
                                <p class="card-description">[Testimonial]</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-player">
                        <div class="card card-plain">
                            <div class="col-md-6 ml-auto mr-auto">
                                <img src="https://carlalexander.ca/app/uploads/2015/10/carl-120px.jpg" class="img-raised rounded-circle img-fluid">
                            </div>
                            <h4 class="card-title">Tom McFarlin</h4>
                            <div class="card-body">
                                <p class="card-description">[Testimonial]</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-player">
                        <div class="card card-plain">
                            <div class="col-md-6 ml-auto mr-auto">
                                <img src="https://carlalexander.ca/app/uploads/2015/10/carl-120px.jpg" class="img-raised rounded-circle img-fluid">
                            </div>
                            <h4 class="card-title">Tonya Mork</h4>
                            <div class="card-body">
                                <p class="card-description">[Testimonial]</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-player">
                        <div class="card card-plain">
                            <div class="col-md-6 ml-auto mr-auto">
                                <img src="https://carlalexander.ca/app/uploads/2015/10/carl-120px.jpg" class="img-raised rounded-circle img-fluid">
                            </div>
                            <h4 class="card-title">Josh Pollock</h4>
                            <div class="card-body">
                                <p class="card-description">[Testimonial]</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-player">
                        <div class="card card-plain">
                            <div class="col-md-6 ml-auto mr-auto">
                                <img src="https://carlalexander.ca/app/uploads/2015/10/carl-120px.jpg" class="img-raised rounded-circle img-fluid">
                            </div>
                            <h4 class="card-title">Tessa Kriesel</h4>
                            <div class="card-body">
                                <p class="card-description">[Testimonial]</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-player">
                        <div class="card card-plain">
                            <div class="col-md-6 ml-auto mr-auto">
                                <img src="https://carlalexander.ca/app/uploads/2015/10/carl-120px.jpg" class="img-raised rounded-circle img-fluid">
                            </div>
                            <h4 class="card-title">Alain Schlesser</h4>
                            <div class="card-body">
                                <p class="card-description">[Testimonial]</p>
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
                            <h6 class="card-category text-info">The book</h6>
                            <h1 class="card-title"><small class="currency">$</small>39</h1>
                            <ul class="list-unstyled">
                                <li><strong>The 166-page</strong> book in PDF format</li>
                                <li><strong>Lifetime access</strong> to book updates</li>
                            </ul>
                            <a href="" class="btn btn-primary btn-round">Buy now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-pricing card-raised bg-primary">
                        <div class="card-body">
                            <h6 class="card-category text-white">Premium package</h6>
                            <h1 class="card-title"><small class="currency">$</small>99</h1>
                            <ul class="list-unstyled">
                                <li>Set of <strong>18 exercises</strong> + <strong>solutions</strong></li>
                                <li><strong>The 166-page</strong> book in PDF format</li>
                                <li><strong>Lifetime access</strong> to book updates</li>
                            </ul>
                            <a href="#pablo" class="btn bg-light btn-outline-primary btn-round">Buy now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-pricing card-plain">
                        <div class="card-body">
                            <h6 class="card-category text-info">Complete package</h6>
                            <h1 class="card-title"><small class="currency">$</small>249</h1>
                            <ul class="list-unstyled">
                                <li><strong>In-depth screencasts</strong> covering all the exercises</li>
                                <li>Set of <strong>18 exercises</strong> + <strong>solutions</strong></li>
                                <li><strong>The 166-page</strong> book in PDF format</li>
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
                    <p>You can get <strong>unlimited licenses of the complete package</strong> as well as a <strong>4 hour code review package</strong> (an $800 value) for <strong>$999</strong>. A great value if you're looking to improve your team's development practices!</p>
                </div>
                <div class="col-md-4 text-center d-flex">
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
                    <p>I'm the charming guy that you can see above. I've been teaching everything I know about programming to the WordPress community for a few years now. Most of it you can find on <a href="https://carlalexander.ca">my site</a> but I've also done a fair share of <a href="https://wordpress.tv/speakers/carl-alexander/">speaking at WordCamps</a> as well.</p>
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
                    <p>Yup, that's not a problem at all! You can send me an email at <a href="mailto:carl@carlalexander.ca">carl@carlalexander.ca</a> and I'll help you out with that.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-5 ml-auto">
                    <h4 class="info-title">What if I end up not liking it?</h4>
                    <p>Then I don't want your money. Seriously! Just email me at <a href="mailto:carl@carlalexander.ca">carl@carlalexander.ca</a> and I'll refund your purchase. No questions asked.</p>
                </div>
                <div class="col-md-5 mr-auto">
                    <h4 class="info-title">I still have a question!</h4>
                    <p>I'm happy to answer any other questions you might have! You can reach me at <a href="mailto:carl@carlalexander.ca">carl@carlalexander.ca</a> and I'll answer you as soon as possible.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="footer footer-default">
    <div class="container copyright">&copy; <?= date('Y'); ?> Carl Alexander. All Rights Reserved.</div>
</footer>
</body>

</html>
