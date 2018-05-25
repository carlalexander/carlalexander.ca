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

    <link href="<?php echo get_stylesheet_directory_uri(); ?>/css/material-kit.min.css" rel="stylesheet" />
</head>

<body <?php body_class(); ?>>

<div class="page-header header-filter" data-parallax="true" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/images/discover-top-desk.jpg');">
    <div class="container">
        <div class="row">
            <div class="offset-md-6 col-md-6">
                <h1 class="title">Wish you could learn object-oriented programming?</h1>
                <p class="h4"><em>"Discover object-oriented programming using WordPress"</em> is a book and video course <u><span class="font-weight-bold">designed for WordPress developers.</span></u> It'll teach you the fundamentals of object-oriented programming using WordPress concepts and terminology. This way you can get the hang of this important topic so that you can <u><span class="font-weight-bold">start using it in your WordPress projects.</span></u></p>
                <p>
                    <a href="" class="btn btn-primary btn-raised btn-round btn-lg font-weight-bold">View packages</a> <a href="" class="bg-light btn btn-outline-primary btn-round btn-lg font-weight-bold border-0">Get a sample</a>
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
                    <div class="col-md-8 ml-auto mr-auto">
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
        <div class="subscribe-line subscribe-line-image" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/images/escheresque_ste.png');">
            <div class="container">
                <div class="row">
                    <div class="col-md-7 ml-auto mr-auto">
                        <div class="text-center">
                            <h3 class="title text-white">Get a free sample</h3>
                            <p class="description text-white">
                                You'll also join my book launch list. This will give you access to free lessons and an early discount when I release the book.
                            </p>
                        </div>
                        <div class="card card-raised card-form-horizontal">
                            <div class="card-body">
                                <form id="ck_subscribe_form" action="https://forms.convertkit.com/landing_pages/167881/subscribe" data-remote="true">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <span class="form-group bmd-form-group">
                                                <input type="text" name="first_name" id="ck_firstNameField" placeholder="First name" class="form-control">
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
</div>

<!--<div class="main main-raised">-->
<!--    <div class="container">-->
<!--        <div class="section">-->

<!--        </div>-->
<!--        <div class="section text-center">-->
<!--            <h2 class="title">Here is our team</h2>-->
<!--            <div class="team">-->
<!--                <div class="row">-->
<!--                    <div class="col-md-4">-->
<!--                        <div class="team-player">-->
<!--                            <div class="card card-plain">-->
<!--                                <div class="col-md-6 ml-auto mr-auto">-->
<!--                                    <img src="../assets/img/faces/avatar.jpg" alt="Thumbnail Image" class="img-raised rounded-circle img-fluid">-->
<!--                                </div>-->
<!--                                <h4 class="card-title">Gigi Hadid-->
<!--                                    <br>-->
<!--                                    <small class="card-description text-muted">Model</small>-->
<!--                                </h4>-->
<!--                                <div class="card-body">-->
<!--                                    <p class="card-description">You can write here details about one of your team members. You can give more details about what they do. Feel free to add some-->
<!--                                        <a href="#">links</a> for people to be able to follow them outside the site.</p>-->
<!--                                </div>-->
<!--                                <div class="card-footer justify-content-center">-->
<!--                                    <a href="#pablo" class="btn btn-link btn-just-icon"><i class="fa fa-twitter"></i></a>-->
<!--                                    <a href="#pablo" class="btn btn-link btn-just-icon"><i class="fa fa-instagram"></i></a>-->
<!--                                    <a href="#pablo" class="btn btn-link btn-just-icon"><i class="fa fa-facebook-square"></i></a>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-md-4">-->
<!--                        <div class="team-player">-->
<!--                            <div class="card card-plain">-->
<!--                                <div class="col-md-6 ml-auto mr-auto">-->
<!--                                    <img src="../assets/img/faces/christian.jpg" alt="Thumbnail Image" class="img-raised rounded-circle img-fluid">-->
<!--                                </div>-->
<!--                                <h4 class="card-title">Christian Louboutin-->
<!--                                    <br>-->
<!--                                    <small class="card-description text-muted">Designer</small>-->
<!--                                </h4>-->
<!--                                <div class="card-body">-->
<!--                                    <p class="card-description">You can write here details about one of your team members. You can give more details about what they do. Feel free to add some-->
<!--                                        <a href="#">links</a> for people to be able to follow them outside the site.</p>-->
<!--                                </div>-->
<!--                                <div class="card-footer justify-content-center">-->
<!--                                    <a href="#pablo" class="btn btn-link btn-just-icon"><i class="fa fa-twitter"></i></a>-->
<!--                                    <a href="#pablo" class="btn btn-link btn-just-icon"><i class="fa fa-linkedin"></i></a>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="col-md-4">-->
<!--                        <div class="team-player">-->
<!--                            <div class="card card-plain">-->
<!--                                <div class="col-md-6 ml-auto mr-auto">-->
<!--                                    <img src="../assets/img/faces/kendall.jpg" alt="Thumbnail Image" class="img-raised rounded-circle img-fluid">-->
<!--                                </div>-->
<!--                                <h4 class="card-title">Kendall Jenner-->
<!--                                    <br>-->
<!--                                    <small class="card-description text-muted">Model</small>-->
<!--                                </h4>-->
<!--                                <div class="card-body">-->
<!--                                    <p class="card-description">You can write here details about one of your team members. You can give more details about what they do. Feel free to add some-->
<!--                                        <a href="#">links</a> for people to be able to follow them outside the site.</p>-->
<!--                                </div>-->
<!--                                <div class="card-footer justify-content-center">-->
<!--                                    <a href="#pablo" class="btn btn-link btn-just-icon"><i class="fa fa-twitter"></i></a>-->
<!--                                    <a href="#pablo" class="btn btn-link btn-just-icon"><i class="fa fa-instagram"></i></a>-->
<!--                                    <a href="#pablo" class="btn btn-link btn-just-icon"><i class="fa fa-facebook-square"></i></a>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--        <div class="section section-contacts">-->
<!--            <div class="row">-->
<!--                <div class="col-md-8 ml-auto mr-auto">-->
<!--                    <h2 class="text-center title">Work with us</h2>-->
<!--                    <h4 class="text-center description">Divide details about your product or agency work into parts. Write a few lines about each one and contact us about any further collaboration. We will responde get back to you in a couple of hours.</h4>-->
<!--                    <form class="contact-form">-->
<!--                        <div class="row">-->
<!--                            <div class="col-md-6">-->
<!--                                <div class="form-group">-->
<!--                                    <label class="bmd-label-floating">Your Name</label>-->
<!--                                    <input type="email" class="form-control">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="col-md-6">-->
<!--                                <div class="form-group">-->
<!--                                    <label class="bmd-label-floating">Your Email</label>-->
<!--                                    <input type="email" class="form-control">-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="form-group">-->
<!--                            <label for="exampleMessage" class="bmd-label-floating">Your Message</label>-->
<!--                            <textarea type="email" class="form-control" rows="4" id="exampleMessage"></textarea>-->
<!--                        </div>-->
<!--                        <div class="row">-->
<!--                            <div class="col-md-4 ml-auto mr-auto text-center">-->
<!--                                <button class="btn btn-primary btn-raised">-->
<!--                                    Send Message-->
<!--                                </button>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </form>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<footer class="footer footer-default" >
</footer>
</body>

</html>
