<?php

session_start();

if ($_SESSION["language"] == "vn") {
    require_once("./localization/vn.php");
} else {
    require_once("./localization/en.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700,800,900"
    rel="stylesheet" />

  <title>Home - GreeLiving for Job-seekers</title>

  <!-- Bootstrap core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">


  <!-- Additional CSS Files -->
  <link rel="stylesheet" href="/assets/css/fontawesome.css" />
  <link rel="stylesheet" href="/assets/css/home.css" />
  <link href="/assets/css/header.css" rel="stylesheet" />
  <link href="/assets/css/footer.css" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/owl.css" />
</head>

<body>
  <!--header-->
  <?php require("./components/header_applicant.php") ?>

  <!-- ***** Main Banner Area Start ***** -->
  <section class="section main-banner" id="section1">
    <video autoplay muted loop id="bg-video">
      <source src="/assets/images/course-video.mp4" type="video/mp4" />
    </video>

    <div class="video-overlay header-text">
      <div class="caption">
        <h6><?= $content["liveIdealGlobal"] ?></h6>
        <h2><em>Greeliving</em></h2>
        <h3><?= $content["platform"] ?></h3>
        <div class="main-button">
          <div class="scroll-to-section">
            <a href="#section2"><?= $content["exploreMore"] ?></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section why-us" id="section2">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="section-heading">
            <h2><?= $content["whyUs"] ?></h2>
          </div>
        </div>
        <div class="col-md-12">
          <div id="tabs">
            <ul>
              <li><a href="#tabs-1"><?= $content["bestEdu"] ?></a></li>
              <li><a href="#tabs-2"><?= $content["topMan"] ?></a></li>
              <li><a href="#tabs-3"><?= $content["qualMeeting"] ?></a></li>
            </ul>
            <section class="tabs-content">
              <article id="tabs-1">
                <div class="row">
                  <div class="col-md-6">
                    <img src="/assets/images/choose-us-image-01.png" alt="" />
                  </div>
                  <div class="col-md-6">
                    <h4><?= $content["bestEdu"] ?></h4>
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing elit. Minima recusandae debitis ducimus maxime necessitatibus consectetur hic neque molestiae repellendus, iste animi voluptas alias sit nostrum error maiores deleniti temporibus dolorem culpa odio! Magni doloremque numquam aliquid, in quasi, tempora animi fuga ea nihil eum, sunt possimus? Ad aliquam repellat libero!
                    </p>
                  </div>
                </div>
              </article>
              <article id="tabs-2">
                <div class="row">
                  <div class="col-md-6">
                    <img src="/assets/images/choose-us-image-02.png" alt="" />
                  </div>
                  <div class="col-md-6">
                    <h4><?= $content["topMan"] ?></h4>
                    <p>
                      Lorem ipsum dolor sit, amet consectetur adipisicing elit. Deserunt earum enim optio maxime inventore velit vero magnam voluptatibus maiores veritatis! Quam magnam, assumenda, officia hic error blanditiis illo laborum earum provident similique ex odio esse consequuntur qui quisquam animi doloremque. Porro temporibus neque asperiores nostrum, tempora libero dolor mollitia sequi!
                    </p>
                  </div>
                </div>
              </article>
              <article id="tabs-3">
                <div class="row">
                  <div class="col-md-6">
                    <img src="/assets/images/choose-us-image-03.png" alt="" />
                  </div>
                  <div class="col-md-6">
                    <h4><?= $content["qualMeeting"] ?></h4>
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing elit. Rerum quam veritatis amet veniam temporibus et reiciendis eveniet perspiciatis laudantium ipsa impedit sit, eos quos maxime odio dignissimos laborum autem? Libero eum omnis ab tempora possimus quasi quo explicabo excepturi sed eaque molestiae, deserunt laudantium eius aliquid blanditiis totam neque molestias.
                    </p>
                  </div>
                </div>
              </article>
            </section>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section coming-soon" id="section3">
    <div class="container">
      <div class="row">
        <div class="col-md-7 col-xs-12">
          <div class="continer centerIt">
            <div>
              <h4>
              <?= $content["platform"] ?>
              </h4>
              <p>
              <?= $content["description"] ?>
              </p>
            </div>
            <div class="introduce-box">
              <div class="column">
                <i class="fa fa-diamond"></i>
                <p><span class="number">11+</span><br><?= $content["experience"] ?></p>
              </div>
              <div class="column">
                <i class="fa fa-graduation-cap"></i>
                <p><span class="number">1000+</span><br><?= $content["grads"] ?></p>
              </div>
              <div class="column">
                <i class="fa fa-building"></i>
                <p><span class="number">100+</span><br><?= $content["partners"] ?>
            </div>
          </div>
        </div>
      </div>
        <div class="col-md-5">
          <div class="right-content">
            <div class="top-content">
              <h6 style="font-weight: bold; text-transform: capitalize;">
                <?= $content["register"] ?>
              </h6>
            </div>
            <form id="contact" action="" method="get">
              <div class="row">
                <div class="col-md-12">
                  <fieldset>
                    <input name="name" type="text" class="form-control" id="name" placeholder="Name" required="" />
                  </fieldset>
                </div>
                <div class="col-md-12">
                  <fieldset>
                    <input name="email" type="text" class="form-control" id="email" placeholder="Email" required="" />
                  </fieldset>
                </div>
                <div class="col-md-12">
                  <fieldset>
                    <input name="phone-number" type="text" class="form-control" id="phone-number"
                      placeholder="Phone" required="" />
                  </fieldset>
                </div>
                <div class="col-md-12">
                  <fieldset>
                    <button type="submit" id="form-submit" class="button">
                    <?= $content["send"] ?>
                    </button>
                  </fieldset>
                </div>
              </div>
            </form>
          </div>
        </div>
    </div>
  </section>

  <section class="section contact" id="section6">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="section-heading">
            <h2><?= $content["contactText"] ?></h2>
          </div>
        </div>
        <div class="col-md-6">
          <form id="contact" action="" method="post">
            <div class="row">
              <div class="col-md-6">
                <fieldset>
                  <input name="name" type="text" class="form-control" id="name" placeholder="Your Name" required="" />
                </fieldset>
              </div>
              <div class="col-md-6">
                <fieldset>
                  <input name="email" type="text" class="form-control" id="email" placeholder="Your Email"
                    required="" />
                </fieldset>
              </div>
              <div class="col-md-12">
                <fieldset>
                  <textarea name="message" rows="6" class="form-control" id="message" placeholder="Your message..."
                    required=""></textarea>
                </fieldset>
              </div>
              <div class="col-md-12">
                <fieldset>
                  <button type="submit" id="form-submit" class="button">
                    Send Message Now
                  </button>
                </fieldset>
              </div>
            </div>
          </form>
        </div>
        <div class="col-md-6">
          <div id="map">

            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.027513592054!2d105.7804593759181!3d21.03158508768023!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135bfa667a7dee9%3A0x2ac9ba5f99e4f389!2sSwinburne%20Innovation%20Space!5e0!3m2!1sen!2s!4v1699090160212!5m2!1sen!2s"
              width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php require("./components/footer.php") ?>

  <script src="/vendor/jquery/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
</body>

</html>